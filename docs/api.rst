API ``get_courses.php``
=======================

Endpoint interno que alimenta a galeria.

.. code-block:: text

   GET {wwwroot}/blocks/course_gallery/api/get_courses.php

Parâmetros de consulta
----------------------

.. list-table::
   :header-rows: 1
   :widths: 18 14 14 54

   * - Parâmetro
     - Tipo
     - Padrão
     - Descrição
   * - ``page``
     - inteiro
     - ``0``
     - Página, base 0. Valores negativos viram ``0``.
   * - ``limit``
     - inteiro
     - ``8``
     - Cursos por página. Mínimo ``1``.
   * - ``blockinstanceid``
     - inteiro
     - **obrigatório**
     - ID da instância do bloco. Usado para carregar as configurações de escopo (categorias). Obriga HTTP ``400`` se ausente ou inválido.
   * - ``search``
     - texto
     - vazio
     - Trecho do nome completo (``LIKE`` sem diferenciar caixa).
   * - ``workload``
     - ``min,max``
     - vazio
     - Intervalo de carga horária. Um único valor equivale a ``min = max``. Se
       ``min > max``, são trocados. ``0`` (ou vazio) desativa o filtro.
   * - ``certificate``
     - lista CSV
     - vazio
     - Valores aceitos de ``tem_certificado`` (comparação frouxa ``==``).
   * - ``learningpath``
     - lista CSV de ids
     - vazio
     - IDs de trilhas. Cada valor deve ser numérico e positivo; caso contrário responde
       ``400``.
   * - ``lang``
     - texto
     - —
     - Enviado pelo front-end, mas **ignorado** pela API. Ver :doc:`limitations`.

Resposta de sucesso
-------------------

.. code-block:: json

   {
     "total": 12,
     "baseurl": "https://moodle.exemplo.br",
     "courses": [
       {
         "has_certificate": "1",
         "workload": "40",
         "lang": "pt_br",
         "id": "17",
         "fullname": "Introdução ao Moodle",
         "category_name": "Extensão",
         "category_url": "https://moodle.exemplo.br/course/management.php?categoryid=3",
         "image_url": "https://moodle.exemplo.br/pluginfile.php/...",
         "url": "https://moodle.exemplo.br/course/view.php?id=17"
       }
     ]
   }

``total`` é a quantidade de cursos que passaram por **todos** os filtros (não apenas os
da página). ``courses`` contém só a fatia da página. Se o curso não tem imagem, usa-se
``pix/default-course-image.webp``.

Erros
-----

.. list-table::
   :header-rows: 1
   :widths: 15 35 50

   * - HTTP
     - Corpo
     - Causa
   * - 200
     - ``{"error": "Access denied."}``
     - Requisição sem cabeçalho ``Referer`` ou de outro host.
   * - 400
     - ``{"error": "Parâmetro 'blockinstanceid' é obrigatório."}``
     - Parâmetro ``blockinstanceid`` ausente ou igual a zero.
   * - 400
     - ``{"error": "Instância de bloco inválida."}``
     - ``blockinstanceid`` fornecido não foi encontrado na tabela ``{block_instances}``.
   * - 400
     - ``{"error": "Parâmetro 'learningpath' inválido..."}``
     - Valor não numérico ou ≤ 0 em ``learningpath``.

.. note::

   A recusa por origem responde com **status 200**, e não 403.

Consulta executada
------------------

.. code-block:: text

   SELECT c.id, c.fullname, c.category
   FROM {course} c INNER JOIN {enrol} e ON (c.id = e.courseid)
   WHERE c.visible = 1 AND c.id != 1 AND e.enrol = 'self' AND e.status = 0
     [AND LOWER(fullname) LIKE LOWER(:query)]
     [AND id IN (SELECT courseid FROM {suap_learning_path_course}
                 WHERE learningpathid IN (...))]
   ORDER BY c.id DESC

Os filtros de carga horária e certificado **não** estão no SQL: são aplicados em PHP,
curso a curso, depois de carregar os campos personalizados. Por isso, cada requisição
percorre todos os cursos elegíveis antes de paginar.

Segurança e considerações
-------------------------

* **Sem autenticação**: o script não chama ``require_login()`` (o ``phpcs`` é
  silenciado deliberadamente). Qualquer requisição com ``Referer`` do mesmo host é
  atendida; o cabeçalho ``Referer`` é facilmente forjável e **não** é um controle de
  segurança robusto. Os dados expostos são apenas de cursos visíveis com autoinscrição.
* **SQL**: ``search`` e ``learningpath`` usam parâmetros nomeados
  (``get_in_or_equal``), sem concatenação de valores do usuário.
* **Categoria ausente**: o código assume que a categoria do curso existe no mapa.
* **Desempenho**: sem cache e com carregamento de campos personalizados por curso
  (N+1); catálogos muito grandes tendem a ficar lentos.
