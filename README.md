# Bloco Galeria de Cursos (`block_course_gallery`)

Este é um bloco personalizado do Moodle desenvolvido para a suíte de temas do IFRN (`suap-moodletheme-suite`). O plugin permite exibir uma galeria interativa e responsiva de cursos abertos com autoinscrição ativa.

## 🚀 Funcionalidades

- **Galeria Dinâmica**: Apresenta os cursos em formato de cartões com imagens personalizadas do curso.
- **Filtros Avançados (Modal)**:
  - **Pesquisa textual**: Busca por termo no nome do curso.
  - **Carga Horária**: Controle deslizante dinâmico de intervalo (utilizando a biblioteca `noUiSlider`) baseado no campo customizado `carga_horaria`.
  - **Certificado**: Filtra os cursos que emitem certificado baseado no campo customizado `tem_certificado`.
  - **Idioma**: Filtra os cursos pelo idioma do conteúdo (`linguagem_conteudo`).
  - **Trilha de Aprendizagem**: Filtra por trilhas de aprendizagem cadastradas no banco de dados (`suap_learning_path_course`).
- **Estados Visuais Fluídos (Skeleton Loader)**: Exibe animações de carregamento (*skeleton screen*) nos cartões de cursos enquanto os dados são obtidos de forma assíncrona.
- **Paginação integrada**: Paginação Ajax rápida e dinâmica para navegação suave entre as páginas.
- **API Segura**: Endpoint exclusivo (`api/get_courses.php`) com validação de origem de requisição (*HTTP Host/Referer*).
- **Internacionalização**: Totalmente traduzido em Português do Brasil (`pt_br`) e Inglês (`en`).

## 🛠️ Requisitos e Configuração Prévia

Para o funcionamento pleno dos filtros do bloco, o Moodle deve conter os seguintes campos de perfil do curso ou tabelas personalizadas:

1. **Campos Customizados de Curso**:
   - `carga_horaria` (Inteiro/Texto): Representa a carga horária em horas do curso.
   - `tem_certificado` (Checkbox/Booleano): Indica se o curso emite certificado.
   - `linguagem_conteudo` (Texto/Menu): Indica o idioma do curso (ex: `pt_br`, `es`, `en`).
2. **Trilha de Aprendizagem (Tabela customizada)**:
   - Tabela `{suap_learning_path_course}` para mapear a associação entre cursos e trilhas de aprendizagem.

## ⚙️ Configuração do Bloco

Ao adicionar o bloco a uma página (como a página inicial do site ou painel do usuário), os seguintes parâmetros podem ser configurados:

- **Título Principal**: Título exibido no cabeçalho do bloco (padrão: *Cursos abertos do IFRN*).
- **Cursos por página**: Define o limite máximo de cursos exibidos simultaneamente por página (padrão: `9`).

## 📥 Instalação

1. Baixe ou clone o repositório dentro do diretório de blocos do seu Moodle:
   ```bash
   cd /caminho/do/seu/moodle/blocks
   git clone https://github.com/ifrn/block_course_gallery.git course_gallery
   ```
   *(Certifique-se de que a pasta de destino seja nomeada como `course_gallery`)*.
2. Acesse a administração do seu Moodle (`/admin`) para disparar o processo de atualização de banco de dados e detecção de novas versões.
3. Configure os direitos de exibição e adicione o bloco nas páginas permitidas (Página inicial ou Painel).

## 📂 Estrutura do Projeto

- `block_course_gallery.php`: Classe base do bloco que renderiza o esqueleto inicial e chama o Javascript do frontend.
- `edit_form.php`: Definição do formulário de configuração do bloco.
- `version.php`: Definição da versão do bloco e dependências do Moodle.
- `api/get_courses.php`: Script PHP que processa as requisições AJAX, filtra a consulta de cursos e retorna JSON.
- `amd/src/main.js`: Lógica Javascript (módulo AMD) para carregar os cursos via AJAX, gerenciar a paginação e os filtros.
- `amd/src/noUiSlider.js`: Módulo de inicialização e customização do *slider* de carga horária.
- `templates/`: Arquivos Mustache contendo os templates do bloco (`header.mustache` e `pagination.mustache`).
- `styles.css`: Estilos e classes visuais para os filtros, paginação e *skeleton screens*.
- `lang/`: Diretório de tradução com suporte para `pt_br` e `en`.
- `health.php`: Arquivo para checagem rápida do status e versão do plugin.

## 📄 Licença

Este plugin é distribuído sob os termos da licença **GNU GPL v3 ou posterior**. Veja o arquivo [LICENSE](file:///C:/Users/2080882/projetos/IFRN/suap-moodletheme-suite/block_course_gallery/LICENSE) para mais detalhes.
