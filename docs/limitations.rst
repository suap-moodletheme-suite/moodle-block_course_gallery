Limitações e pontos de atenção
==============================

Levantados a partir da leitura do código da versão |release|. Não são exigências de uso,
mas comportamentos que quem instala ou evolui o plugin precisa conhecer.

.. list-table::
   :header-rows: 1
   :widths: 5 40 55

   * - #
     - Ponto
     - Detalhe
   * - 1
     - **Filtro de idioma não é aplicado**
     - O front-end envia ``lang``, mas ``api/get_courses.php`` não lê esse parâmetro.
       Marcar idiomas altera o *badge* mas não a lista.
   * - 2
     - **Filtro de trilha sem opções**
     - O template itera ``{{#learningpaths}}``, mas ``get_content()`` não fornece esse
       dado; a coluna aparece vazia. A API já suporta ``learningpath``.
   * - 3
     - **API sem autenticação e com controle por Referer**
     - Ver :doc:`api`. O ``Referer`` é forjável; a recusa retorna HTTP 200.
   * - 4
     - **Filtragem em PHP e N+1**
     - Carga horária e certificado são filtrados após carregar campos de cada curso; não
       há cache.
   * - 5
     - **Campos personalizados obrigatórios**
     - A ausência de ``carga_horaria``, ``tem_certificado`` ou ``linguagem_conteudo``
       gera avisos PHP.
   * - 6
     - **Dependência do theme_suap e de CDN**
     - Sem o tema, faltam strings e o ícone de certificado; sem acesso ao jsDelivr, o
       slider não é carregado.
   * - 7
     - **Sem escape no front-end**
     - Nome do curso e categoria via ``innerHTML``; ``search`` sem
       ``encodeURIComponent``; título do bloco com chaves triplas.
   * - 8
     - **Busca sem debounce**
     - Uma requisição por tecla.
   * - 9
     - **Padrões inconsistentes de "cursos por página"**
     - ``3`` (``specialization``), ``9`` (formulário e JS), ``8`` (API).
   * - 10
     - **Textos fixos em português**
     - Título padrão e rótulo "Trilha" fora dos arquivos de idioma; ``pluginname`` não é
       traduzido.
   * - 11
     - **Suíte de testes PHPUnit**
     - O repositório possui suíte de testes unitários para a classe ``course_repository`` (testes de escopo por categoria e visibilidade em ``tests/course_repository_test.php``).
   * - 12
     - **Slider fixo em 10–100 h**
     - Cursos fora desse intervalo só aparecem com o filtro desativado.
