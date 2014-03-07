# -*- coding: utf-8 -*-
"""Fabfile de procedimientos de deployment.

En el presente fabfile se desarrolla una serie de procedimientos que automatizan
las actividades de deployment para el presente proyecto.
"""

import os

from fabric.api import *
from fabric.colors import *

# Definimos algunos datos sobre dónde y con qué identidad ejecutaremos los
# comandos, normalmente no hay que cambiar nada ya que en el servidor de
# producción esto no debería de variar ni se usará este archivo para correrse
# localmente.

env.hosts = ['localhost']
env.user = 'jenkins'
env.password = 'j3nk1s**111'


# También se definen las rutas base, del directorio de la marca, y el directorio
# que se copiará (en orden de aparición), al final se mezcla todo para obtener
# la ruta de trabajo completa.  No son necesarios los slashes al final (se
# observa que DEPLOY_PATH los incluye)
DEPLOY_BASE_PATH = '/var/www/nginx/2014'
BRAND_FOLDER = 'cusquena'
DEPLOY_FOLDER = 'cusquena-chapas2-staging'
# DEPLOY_FOLDER = os.getcwd().split('/')[-1]  # Puede ser, por ejemplo 'sitio-abc'

# Si se desea que script ingrese a la base de datos y cree una base de datos
# nueva (o la limpie si existe), rellenar los datos de acceso y el nombre de la
# DB, y si además se desea cargar un dump, colocar su nombre (debe estar en el
# repositorio, junto a este archivo), de lo contrario dejar en blanco como
# corresponda.
DATABASE_USERNAME = 'root'
DATABASE_PASSWORD = '*sql$$cr1xus*'
DATABASE_NAME = ''
DUMP_FILE = ''

# Normalización del password de la base de datos, no debería de tocarse.
DATABASE_PASSWORD = DATABASE_PASSWORD.replace('$', '\\$')

# Esto junta las rutas escritas en la sección anterior, normalmente no tendría
# por qué ser editado.
BASE_BRAND_PATH = '%s/%s' % (DEPLOY_BASE_PATH, BRAND_FOLDER,)
DEPLOY_PATH = '%s/%s/' % (BASE_BRAND_PATH, DEPLOY_FOLDER,)


@task
def deploy_staging():
    """Staging deployment process."""

    print cyan('Deploy staging begins.')

    # Teardown: donde normalmente se limpia todo. Se observa la destrucción de
    # la base de datos (si se dieron los datos) y el directorio anterior
    # del proyecto.
    print red('Performing teardown procedures...')
    sudo('rm -rf %s' % DEPLOY_PATH)
    if DATABASE_USERNAME and DATABASE_PASSWORD and DATABASE_NAME:
        run(
            'echo \'DROP DATABASE IF EXISTS %s;\' | mysql -u\'%s\' -p\'%s\''
            % (DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD,),
            shell_escape=False
        )

    # Si se desea realizar alguna operación adicional después del Teardown
    # (y/o antes del Setup), editar la función post_teardown, para mantener
    # esta sección lo más limpia posible.
    print green('Performing post-teardown procedures...')
    post_teardown()

    # Setup: Las operaciones que normalmente se realizan al haberse limpiado el
    # entorno, en este caso, se regenera la base de datos si se suministró el
    # archivo de respaldo de la misma para terminar copiando todo el proyecto a
    # su ubicación final.
    print green('Performing setup procedures...')
    if DATABASE_USERNAME and DATABASE_PASSWORD and DATABASE_NAME:
        run(
            'echo \'CREATE DATABASE %s;\' | mysql -u\'%s\' -p\'%s\''
            % (DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD,),
            shell_escape=False
        )

        if DUMP_FILE:
            run(
                'mysql -u\'%s\' -p\'%s\' %s < %s/jenkins/%s'
                % (DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME,
                   os.getcwd(), DUMP_FILE),
                shell_escape=False
            )

    run('cp -R %s %s' % (os.getcwd(), DEPLOY_PATH))

    # Si en este momento se requiere de alguna serie de operaciones adicionales,
    # estas tienen que definirse en la función post_setup.
    print green('Performing post-setup procedures...')
    post_setup()

    print green('All done (with success, hopefuly)!')


def post_teardown():
    """
    Actividades que se realizan después del Teardown y antes del Setup que son
    específicas para este proyecto únicamente. De otra forma, esta función
    debería de quedar vacía (sólo la sentencia 'pass').
    """
    # run('mkdir -p %s/system/cusquena/cache/default' % DEPLOY_PATH)
    # run('chmod 777 -R %s/system/cusquena/cache' % DEPLOY_PATH)
    pass


def post_setup():
    """
    Actividades a realizarse después del Setup, al finalizar la ejecución
    normal de todas las actividades del script. De otra forma, esta función
    debería de quedar vacía (sólo la sentencia 'pass').
    """
    with cd(DEPLOY_PATH):
        sudo('cp jenkins/cusquena-chapas2-staging.conf /etc/nginx/conf.d/')
	sudo('php composer.phar install')

        with settings(warn_only=True):
            sudo('service nginx reload')


# Apéndice: Fabric in a nutshell
#
# Para ejecutar un comando, utilizar run('') y poner el comando dentro de las
# comillas. Si se necesita usar alguna constante definida al inicio del script,
# escribir '%s' en los lugares necesarios, y acompañar las comillas del símbolo
# % seguido de una lista de las variables (tantas como %s's se hayan definido,
# en orden de aparición, se puede observar un ejemplo del uso en deploy_staging)
#
# Si se necesitan permisos de administrador para ejecutar el comando, cambiar
# 'run' por 'sudo'.
#
# Si el comando puede fallar y no se desea que el script se detenga por este
# motivo, escribir 'with settings(warn_only=True):' e indentar un nivel la(s)
# instruccion(es) que se necesiten ejecutarse de esta manera.
