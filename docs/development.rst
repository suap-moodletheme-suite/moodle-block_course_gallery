Desenvolvimento
===============

Ambiente
--------

Coloque o repositório em ``<moodle>/blocks/course_gallery`` (o nome da pasta importa).
Para o JavaScript, use o Grunt do Moodle:

.. code-block:: bash

   npx grunt amd --root=blocks/course_gallery

Compilando esta documentação
----------------------------

.. code-block:: bash

   pip install -r docs/requirements.txt
   sphinx-build -W -b html docs docs/_build/html

A versão exibida é lida de ``version.php`` por ``docs/conf.py``. O fluxo
``.github/workflows/docs.yml`` faz esse mesmo *build* (com ``-W``) em pushes e PRs e, em
pushes na ``main``, publica o resultado no GitHub Pages.

Integração contínua
-------------------

O fluxo ``.github/workflows/ci.yml`` executa o ``moodle-plugin-ci`` em pushes e *pull
requests* para ``main`` e ``MOODLE_*``, em matriz de PHP 8.1–8.4 × Moodle 4.5/5.0/5.1 ×
PostgreSQL/MariaDB: ``phplint``, ``phpcpd``, ``phpmd``, ``codechecker``, ``phpdoc``,
``validate``, ``savepoints``, ``mustache``, ``grunt``, ``phpunit`` e ``behat``. Alguns
passos são não bloqueantes (``continue-on-error``). O plugin ainda não possui testes
PHPUnit nem Behat.

Versionamento e releases
------------------------

Em ``version.php``:

* ``$plugin->release`` — versão legível (ex.: ``0.1.05``);
* ``$plugin->version`` — versão numérica ``AAAAMMDDNN`` (ex.: ``2025072305``).

O fluxo ``release.yml`` é disparado por *tags* e valida que:

#. os dois últimos dígitos de ``version`` coincidem com o último segmento de ``release``;
#. ``release`` é igual ao nome da *tag* (sem o prefixo ``v``).

Em seguida gera ``block_course_gallery-<version>.zip`` (com a pasta ``course_gallery/``,
sem ``.git``, ``.github``, ``tests`` etc.) e cria a *release* no GitHub.

.. code-block:: bash

   # após atualizar version.php
   git tag -a 0.1.06 -m "Release 0.1.06"
   git push origin 0.1.06

Verificação rápida
------------------

Acessar ``/blocks/course_gallery/health.php`` imprime ``release`` e ``version`` do plugin.
O arquivo não exige login.

Autoria e propriedade intelectual
---------------------------------

Autores em ``AUTHORS.md``. O arquivo ``INPI.md`` reúne os dados para o registro do
programa de computador no INPI.
