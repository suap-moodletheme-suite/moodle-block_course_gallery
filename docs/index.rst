Bloco Galeria de Cursos (``block_course_gallery``)
====================================================

O **Galeria de Cursos** é um bloco do Moodle da suíte de temas SUAP do IFRN
(``suap-moodletheme-suite``). Ele exibe, na página inicial do site ou no painel do
usuário, uma vitrine responsiva dos **cursos abertos com autoinscrição ativa**, com busca
por nome, filtros (carga horária, certificado, idioma e trilha de aprendizagem),
paginação e *skeleton loader* durante o carregamento.

Os dados dos cursos não são renderizados no servidor junto com a página: o bloco entrega
apenas o esqueleto HTML e um módulo AMD, que consulta um endpoint JSON próprio
(``api/get_courses.php``) e monta os cartões no navegador.

.. list-table::
   :widths: 25 75

   * - Componente Moodle
     - ``block_course_gallery`` (instalado em ``blocks/course_gallery``)
   * - Versão
     - |release|
   * - Moodle mínimo
     - 4.1 (``2022112800``); CI executada em 4.5, 5.0 e 5.1
   * - Licença
     - GNU GPL v3 ou posterior
   * - Repositório
     - https://github.com/suap-moodletheme-suite/moodle-block_course_gallery

.. important::

   O bloco foi feito para o ambiente do IFRN e **depende de artefatos externos ao
   plugin**: o tema ``theme_suap`` (strings de idioma e ícone), três campos
   personalizados de curso e, opcionalmente, a tabela ``suap_learning_path_course``. Veja
   :doc:`installation` e :doc:`limitations`.

.. toctree::
   :maxdepth: 2
   :caption: Sumário

   installation
   configuration
   usage
   architecture
   api
   frontend
   i18n
   development
   limitations
