Arquitetura
===========

Estrutura do repositório
------------------------

.. code-block:: text

   block_course_gallery/
   ├── classes/
   │   └── course_repository.php  # Classe de repositório e escopo de cursos por categoria
   ├── block_course_gallery.php   # Classe do bloco (block_base)
   ├── edit_form.php              # Formulário de configuração da instância
   ├── version.php                # Versão, release e requisitos
   ├── health.php                 # Exibe release e version (verificação rápida)
   ├── api/get_courses.php        # Endpoint JSON que lista/filtra cursos
   ├── amd/src/main.js            # Módulo AMD principal (fonte)
   ├── amd/src/noUiSlider.js      # Inicializa o slider de carga horária (fonte)
   ├── amd/build/*.min.js         # Artefatos compilados servidos pelo Moodle
   ├── templates/header.mustache  # Cabeçalho, busca e modal de filtros
   ├── templates/pagination.mustache
   ├── tests/
   │   └── course_repository_test.php # Testes PHPUnit do repositório
   ├── db/access.php              # Capacidades
   ├── lang/{en,pt_br}/           # Strings de idioma
   ├── pix/                       # Ícones e imagem padrão de curso
   └── styles.css                 # Estilos do bloco

Fluxo de execução
-----------------

.. code-block:: text

   Página carrega
     └─ specialization()  → registra CSS/JS (noUiSlider) e chama block_course_gallery/main.init(url, limit)
     └─ get_content()     → header.mustache + 8 cartões skeleton + pagination.mustache
   Navegador
     └─ main.init()       → loadCourses()
          └─ fetch(api/get_courses.php?page&limit&search&workload&certificate&lang&learningpath)
               └─ JSON {total, courses[], baseurl}
          └─ substitui os skeletons pelos cartões, atualiza a paginação
   Interação (busca, filtros, páginas) → loadCourses() novamente

A classe ``block_course_gallery``
---------------------------------

``init()``
   Define o título (string ``pluginname``).

``specialization()``
   Chamado após ``init()``, com a configuração da instância carregada. Zera o título para
   ocultar o cabeçalho padrão do Moodle, carrega o módulo AMD ``block_course_gallery/main``
   com a URL da API e ``max_courses``, adiciona o CSS/JS do noUiSlider via CDN e inicia o
   módulo ``block_course_gallery/noUiSlider``.

``get_content()``
   Renderiza ``header`` com ``gallery_title``, anexa o esqueleto de carregamento
   (``render_courses()``) e a paginação. O conteúdo fica em ``$this->content`` durante a
   requisição.

``render_courses()``
   Gera 8 cartões ``course-gallery-card`` com classes ``skeleton*``, substituídos pelo
   JavaScript.

``applicable_formats()``
   Limita a ``site-index`` e ``my``.

Templates Mustache
------------------

``header.mustache``
   Título, campo de busca (``#search``), botão de filtros (``#filter-courses``) com
   *badge*, sobreposição do modal (``#modal-overlay``) e a área de filtros
   (``#filter-area``) com colunas de carga horária, certificado, idioma e trilha. Usa as
   strings do próprio plugin (``filter``, ``selected_time``), do núcleo e do
   ``theme_suap``. A coluna de trilha itera ``{{#learningpaths}}``.

``pagination.mustache``
   Botões anterior/próxima (``#prev-page``, ``#next-page``), contêiner dos números
   (``#pagination-numbers``) e ícones de ``pix/``.

Dependências externas
---------------------

.. list-table::
   :header-rows: 1
   :widths: 30 70

   * - Dependência
     - Uso
   * - ``theme_suap``
     - Strings (``certificate``, ``workload``, ``pt-br``, ``es``) e ícone de certificado.
   * - noUiSlider 15.7.1 (jsDelivr)
     - Slider de intervalo. Carregado como CSS, como script global e como dependência
       do módulo AMD ``noUiSlider``.
   * - Font Awesome (do tema)
     - Ícones ``fa-search``, ``fa-sliders`` e ``fa-xmark``.
   * - ``\core_course\customfield\course_handler``
     - API de campos personalizados do curso.
   * - ``\core_course\external\course_summary_exporter``
     - Obtenção da imagem do curso.
