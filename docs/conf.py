"""Configuração do Sphinx para a documentação do block_course_gallery."""

import re
from pathlib import Path

import moodle_docs_theme

# A versão vem do version.php, fonte única de verdade do plugin.
_version_php = (Path(__file__).parent.parent / "version.php").read_text(encoding="utf-8")
_match = re.search(r"\$plugin->release\s*=\s*'([^']+)'", _version_php)

project = "block_course_gallery"
copyright = "2025-2026, IFRN — Suíte de temas SUAP Moodle"
author = "Kelson da Costa Medeiros"
release = _match.group(1) if _match else "desconhecida"

extensions = ["sphinx.ext.githubpages", "moodle_docs_theme"]

templates_path = []
exclude_patterns = ["_build"]

language = "pt_BR"

html_theme = "moodle_docs_theme"
html_theme_path = [moodle_docs_theme.get_html_theme_path()]

html_theme_options = {
    "primary_color": "#6c336d",
    "secondary_color": "#f98012",
    "project_name": "Bloco Galeria de Cursos",
    "tagline": "Vitrine de cursos abertos para o Moodle",
    "github_url": "https://github.com/suap-moodletheme-suite/moodle-block_course_gallery",
    "github_repo": "suap-moodletheme-suite/moodle-block_course_gallery",
    "github_version": "main",
    "doc_path": "docs/",
    "show_edit_on_github": True,
    "enable_dark_mode": True,
    "navigation_links": (
        "Início|index, Instalação|installation, Configuração|configuration, "
        "Uso|usage, Arquitetura|architecture, API|api, Desenvolvimento|development"
    ),
}

html_static_path = []
