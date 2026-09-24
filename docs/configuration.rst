Configuração
============

O bloco não possui configurações globais. Cada instância é configurada em seu próprio
formulário (*Editar → Configurar bloco Course Gallery*), definido em
``edit_form.php``.

Onde o bloco pode ser adicionado
--------------------------------

Definido por ``applicable_formats()`` em ``block_course_gallery.php``:

.. list-table::
   :header-rows: 1
   :widths: 30 20

   * - Formato de página
     - Permitido
   * - ``site-index`` (página inicial)
     - Sim
   * - ``my`` (Painel)
     - Sim
   * - ``course-view``
     - Não
   * - ``mod``
     - Não
   * - ``admin``
     - Não

Parâmetros da instância
-----------------------

.. list-table::
   :header-rows: 1
   :widths: 22 18 20 40

   * - Campo
     - Chave (``config_*``)
     - Padrão
     - Efeito
   * - Título principal
     - ``gallery_title``
     - ``Cursos abertos do IFRN``
     - Texto do ``<h2>`` no cabeçalho do bloco. É renderizado com chaves triplas no
       Mustache (sem escape de HTML).
   * - Categorias de cursos
     - ``categories``
     - Vazio
     - Autocompletar múltiplo das categorias cujos cursos (e subcategorias elegíveis) serão exibidos na galeria. Se nenhuma categoria estiver configurada, a galeria permanecerá vazia.
   * - Cursos por página
     - ``max_courses``
     - ``9`` no formulário
     - Tamanho da página enviado à API como ``limit``.

.. note::

   O título padrão do bloco em si (``pluginname``) é suprimido: ``specialization()``
   define ``$this->title = ''``. Só o título principal configurado aparece.

.. note::

   Há três valores padrão distintos para "cursos por página": ``9`` no formulário,
   ``3`` em ``specialization()`` (quando a instância ainda não foi configurada) e ``9`` no
   JavaScript (quando o valor recebido é falsy). Instâncias nunca configuradas usam ``3``.

Parâmetros fixos no código
--------------------------

Estes valores não são configuráveis pela interface; alterá-los exige editar o código:

.. list-table::
   :header-rows: 1
   :widths: 35 25 40

   * - Parâmetro
     - Valor
     - Local
   * - Intervalo do slider de carga horária
     - 10 a 100 h, passo 10
     - ``amd/src/noUiSlider.js``
   * - Cartões do *skeleton*
     - 8
     - ``block_course_gallery::render_courses()``
   * - Versão do noUiSlider
     - 15.7.1 (CDN jsDelivr)
     - ``specialization()`` e ``amd/src/noUiSlider.js``
   * - Curso excluído da listagem
     - ``id = 1`` (página inicial do site)
     - ``api/get_courses.php``
