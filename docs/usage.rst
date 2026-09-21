Uso
===

Visão do visitante
------------------

Ao abrir a página que contém o bloco, o usuário vê:

#. **Cabeçalho** com o título configurado, um campo de busca e o botão **Filtros**.
#. **Skeleton loader**: oito cartões cinza animados enquanto a API responde.
#. **Cartões de curso**, cada um com imagem, categoria, nome e, quando disponíveis,
   carga horária, selo de certificado e bandeira do idioma. Clicar leva a
   ``/course/view.php?id=<id>``.
#. **Paginação** (anterior, números, próxima), oculta quando há apenas uma página.

Que cursos aparecem
-------------------

Somente cursos que satisfazem **todas** as condições:

* visíveis (``visible = 1``);
* diferentes do curso da página inicial (``id != 1``);
* com um método de inscrição ``self`` (autoinscrição) **habilitado** (``status = 0``).

Os cursos são ordenados do mais novo para o mais antigo (``ORDER BY id DESC``).

Busca
-----

Cada tecla digitada no campo dispara uma nova requisição (não há *debounce*). A busca é
feita por trecho no nome completo do curso, sem diferenciar maiúsculas/minúsculas.

Filtros
-------

O botão **Filtros** abre um modal. As alterações só valem ao clicar em **Aplicar
filtros**; **Limpar filtros** desmarca tudo, restaura o slider e recarrega a lista. Um
*badge* no botão mostra quantos filtros estão ativos.

.. list-table::
   :header-rows: 1
   :widths: 20 30 50

   * - Filtro
     - Controle
     - Comportamento
   * - Carga horária
     - Slider de intervalo (10–100 h)
     - Mantém cursos cuja ``carga_horaria`` está dentro do intervalo, inclusive. Com o
       slider inteiro (10–100), o filtro é considerado inativo. Quando ativo, cursos com
       carga horária ``0`` ou vazia são **excluídos**.
   * - Certificado
     - Caixa "Sim" (valor ``1``)
     - Mantém cursos cujo ``tem_certificado`` é igual a ``1``.
   * - Idioma
     - Caixas pt-br, es, en
     - Ver :doc:`limitations`: o valor é enviado, mas a API atual não o aplica.
   * - Trilha
     - Caixas geradas por trilha
     - Mantém cursos vinculados às trilhas marcadas. Ver :doc:`limitations`.

Filtros de mesma categoria são combinados por **OU** (ex.: trilhas) e categorias
diferentes por **E**.

Paginação
---------

A paginação é feita em memória no servidor: a API filtra todos os cursos elegíveis,
conta o total e devolve apenas a fatia da página pedida. Mudar de página ou de filtro
sempre dispara nova consulta. Ao aplicar filtros ou limpá-los, a página volta a ``0``.
