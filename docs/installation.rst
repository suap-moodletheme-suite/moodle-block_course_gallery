Instalação
==========

Requisitos
----------

.. list-table::
   :header-rows: 1
   :widths: 30 70

   * - Item
     - Detalhe
   * - Moodle
     - 4.1 ou superior (``$plugin->requires = 2022112800``). A integração contínua
       valida 4.5, 5.0 e 5.1.
   * - PHP
     - 8.1 a 8.4, conforme a versão do Moodle (8.1 apenas até o Moodle 4.5; 8.4 apenas
       a partir do 5.0).
   * - Tema ``theme_suap``
     - Fornece as strings ``certificate``, ``workload``, ``pt-br`` e ``es`` e o ícone
       ``pix/checkmark-circle-outline.svg`` usados pelo bloco.
   * - Acesso à internet (CDN)
     - O CSS e o JS do noUiSlider 15.7.1 são carregados de ``cdn.jsdelivr.net``.

Campos personalizados de curso
------------------------------

Crie em *Administração do site → Cursos → Campos personalizados de curso* (o
**nome curto** precisa ser exatamente o indicado):

.. list-table::
   :header-rows: 1
   :widths: 25 20 55

   * - Nome curto
     - Tipo sugerido
     - Uso
   * - ``carga_horaria``
     - Número / texto
     - Carga horária em horas. Exibida no cartão e usada no filtro deslizante.
   * - ``tem_certificado``
     - Caixa de seleção
     - Indica se o curso emite certificado. Exibe o selo no cartão e alimenta o filtro.
   * - ``linguagem_conteudo``
     - Texto / menu
     - Idioma do conteúdo (``pt_br``, ``en``, ``es``). Define a bandeira exibida.

.. warning::

   O endpoint acessa esses campos diretamente (``$customfieldsmetadata->carga_horaria``
   etc.). Se um campo não existir, o PHP emitirá avisos de propriedade indefinida e o
   filtro correspondente não funcionará como esperado.

Trilhas de aprendizagem (opcional)
----------------------------------

O filtro por trilha consulta a tabela ``{suap_learning_path_course}`` (colunas
``courseid`` e ``learningpathid``), que **não é criada por este plugin** — é fornecida
pela suíte SUAP. Sem ela, o uso do parâmetro ``learningpath`` na API resulta em erro de
SQL. Veja :doc:`limitations` para o estado atual da interface desse filtro.

Instalando o plugin
-------------------

.. important::

   O diretório final **deve** se chamar ``course_gallery``: o código monta URLs fixas
   como ``/blocks/course_gallery/api/get_courses.php``.

**Via Git**

.. code-block:: bash

   cd /caminho/do/moodle/blocks
   git clone https://github.com/suap-moodletheme-suite/moodle-block_course_gallery.git course_gallery

**Via ZIP de release**

Baixe o arquivo ``block_course_gallery-<versão>.zip`` da página de *Releases* do GitHub e
envie em *Administração do site → Plugins → Instalar plugins*.

Depois de copiar os arquivos, acesse *Administração do site → Notificações* para concluir
a instalação. O plugin não cria tabelas nem configurações globais.

Permissões
----------

.. list-table::
   :header-rows: 1
   :widths: 35 25 40

   * - Capacidade
     - Contexto
     - Padrão
   * - ``block/course_gallery:addinstance``
     - Bloco
     - Professor editor e Gerente
   * - ``block/course_gallery:myaddinstance``
     - Sistema
     - Usuário autenticado (adicionar ao painel)

Atualização do JavaScript
-------------------------

O Moodle serve o JavaScript de ``amd/build``. Ao alterar ``amd/src``, recompile com o
Grunt do Moodle (``grunt amd``) e, no site, limpe os caches em *Administração do site →
Desenvolvimento → Limpar todos os caches* (ou desative o cache de JS em modo de
desenvolvimento).
