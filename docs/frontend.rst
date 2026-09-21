Front-end (módulos AMD)
=======================

``block_course_gallery/main``
-----------------------------

Definido em ``amd/src/main.js``. Exporta ``init(requestUrl, maxCourses)``, que guarda a
URL da API e o limite (``maxCourses || 9``) e chama ``loadCourses()``.

.. list-table::
   :header-rows: 1
   :widths: 30 70

   * - Função
     - Responsabilidade
   * - ``loadCourses()``
     - Monta a query string (``page``, ``limit``, ``search``, ``workload``,
       ``certificate``, ``lang``, ``learningpath``), faz ``fetch``, substitui o
       conteúdo de ``.course-area`` pelos cartões e chama ``updatePaginationButtons()``.
       Sem resultados, mostra a string ``nomorecourses`` do núcleo.
   * - ``getFilter()``
     - Lê o slider e os checkboxes dos filtros e devolve os valores como CSV.
   * - ``updateFilterBadge()``
     - Atualiza o contador exibido no botão **Filtros**.
   * - ``updatePaginationButtons()``
     - Redesenha os números de página (janela de ±2 páginas, com reticências e as duas
       primeiras/últimas) e habilita/desabilita anterior/próxima.
   * - ``createPageButton(page)`` / ``updateActivePageButton()``
     - Criam e destacam o botão de página atual.
   * - ``selectLangFlag(lang)``
     - ``pt_br`` → 🇧🇷, ``en`` → 🇺🇸, ``es`` → 🇪🇸, demais → 🌐.
   * - ``closeFilter()`` / ``toggleScroll()``
     - Fecham o modal e alternam o ``overflow`` do ``body``.
   * - ``correctMainPadding()``
     - Zera o *padding* horizontal do elemento ``[role="main"]`` para a galeria ocupar
       toda a largura.

Eventos ligados no carregamento do módulo: ``input`` na busca; cliques em anterior,
próxima, abrir/fechar/aplicar/limpar filtro e na sobreposição; ``load`` da janela.

Estrutura de um cartão renderizado
----------------------------------

.. code-block:: text

   <a class="course-gallery-card" id="17" href="{baseurl}/course/view.php?id=17">
     <div class="course-image-container"><img class="course-image" ...></div>
     <span class="course-category">Categoria</span>
     <span class="course-name">Nome do curso</span>
     <div class="course-detail">
       <div class="course-workload">...</div>
       <div class="course-certificate">...</div>
       <div class="course-lang">...</div>
     </div>
   </a>

As linhas de carga horária, certificado e idioma só aparecem quando o valor existe.

``block_course_gallery/noUiSlider``
-----------------------------------

Definido em ``amd/src/noUiSlider.js``; depende de ``jquery`` e do noUiSlider via CDN.
Cria o slider em ``#workload-slider`` com ``start: [10, 100]``, ``range 10–100``,
``step: 10`` e formatação para inteiros. Atualiza ``#workload-range-display`` a cada
mudança e preenche ``#workload-range-total``.

Estilos
-------

``styles.css`` cobre o cabeçalho (``.course-gallery-main-header*``), a grade
(``.course-area``) e o cartão (``.course-gallery-card``), o *skeleton*
(``.skeleton*``), a paginação (``.pagination*``), o modal (``.filter-area``,
``.modal-overlay``) e o *badge*.

.. warning::

   O front-end injeta ``course.fullname`` e ``category_name`` via ``innerHTML`` e envia
   ``search`` sem ``encodeURIComponent``. Isso é relevante se nomes de curso puderem
   conter HTML e para buscas com caracteres como ``&``. Ver :doc:`limitations`.
