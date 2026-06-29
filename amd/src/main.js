define(["core/str"], function (str) {
    let url = '';
    let currentPage = 0;
    let limit = 0;
    let totalCourses = 0;

    const courseArea = document.querySelector('.course-area');
    const searchInput = document.querySelector('#search');
    const pagination = document.querySelector('.pagination');
    const paginationNumbers = document.querySelector('#pagination-numbers');
    const prevPageButton = document.querySelector('#prev-page');
    const nextPageButton = document.querySelector('#next-page');

    /**
     * Fetch and render courses from backend based on search query, page and filters.
     *
     * @returns {Promise<void>}
     */
    async function loadCourses() {
        try {
            const filters = getFilter();
            const queryParams = `page=${currentPage}&limit=${limit}` +
                `&search=${searchInput.value}&workload=${filters.workload}` +
                `&certificate=${filters.certificate}&lang=${filters.lang}` +
                `&learningpath=${filters.learningpath}`;
            // eslint-disable-next-line no-console
            const response = await fetch(`${url}?${queryParams}`)
                // eslint-disable-next-line no-console
                .catch(error => console.error('Error fetching courses:', error));

            const { total, courses, baseurl } = await response.json();
            totalCourses = total;

            courseArea.innerHTML = '';

            if (courses.length == 0) {
                const nomorecourses = await str.get_string('nomorecourses', 'core');
                courseArea.innerHTML = '<p>' + nomorecourses + '</p>';
            }

            const certificateStr = await str.get_string('certificate', 'theme_suap');
            const workloadStr = await str.get_string('workload', 'theme_suap');
            const hoursStr = await str.get_string('hours', 'core');
            const languageStr = await str.get_string('language', 'core');

            courses.forEach(course => {
                const certificateArea = !course.has_certificate ? '' : `
                    <div class="course-certificate">
                        <p class="course-certificate-text">${certificateStr}</p>
                        <span class="course-certificate-value">
                            <img class="course-certificate-value-img"
                                src="${baseurl}/theme/suap/pix/checkmark-circle-outline.svg" alt="">
                        </span>
                    </div>
                `;
                const langArea = !course.lang ? '' : `
                    <div class="course-lang">
                        <p class="course-lang-text">${languageStr}</p>
                        <span class="course-lang-value">${selectLangFlag(course.lang)}</span>
                    </div>
                `;
                const workloadArea = !course.workload ? '' : `
                    <div class="course-workload">
                        <p class="course-workload-text">${workloadStr}</p>
                        <span class="course-workload-value">${course.workload + " " + hoursStr}</span>
                    </div>
                `;
                courseArea.innerHTML += `
                    <a class="course-gallery-card" id="${course.id}" href="${baseurl}/course/view.php?id=${course.id}">
                        <div class="course-image-container">
                            <img src="${course.image_url}" alt="${course.fullname}" class="course-image">
                        </div>
                        <span class="course-category">${course.category_name}</span>
                        <span class="course-name">${course.fullname}</span>
                        <div class="course-detail">
                            ${workloadArea}
                            ${certificateArea}
                            ${langArea}
                        </div>
                    </a>
                `;
            });

            correctMainPadding();
            updatePaginationButtons();
        } catch (error) {
            // eslint-disable-next-line no-console
            console.error('Error fetching courses:', error);
        }
    }

    /**
     * Update the pagination buttons on the screen.
     *
     * @returns {void}
     */
    function updatePaginationButtons() {
        paginationNumbers.innerHTML = '';
        prevPageButton.disabled = currentPage === 0;
        nextPageButton.disabled = (currentPage + 1) * limit >= totalCourses;

        const totalPages = Math.ceil(totalCourses / limit);

        // Possui uma página não precisa de paginação.
        if (totalPages <= 1) {
            pagination.classList.add('disabled');
            return;
        }

        const pageRange = 2;
        let startPage = Math.max(0, currentPage - pageRange);
        let endPage = Math.min(totalPages - 1, currentPage + pageRange);

        if (startPage > 2) {
            createPageButton(0);
            createPageButton(1);
            paginationNumbers.innerHTML += '... ';
        }

        for (let i = startPage; i <= endPage; i++) {
            createPageButton(i);
        }

        if (endPage < totalPages - 3) {
            paginationNumbers.innerHTML += ' ...';
            createPageButton(totalPages - 2);
            createPageButton(totalPages - 1);
        }

        nextPageButton.setAttribute('page', currentPage + 1);
        prevPageButton.setAttribute('page', currentPage - 1);

        pagination.classList.remove('disabled');
    }

    /**
     * Create a pagination page button.
     *
     * @param {number} page The page number.
     * @returns {void}
     */
    function createPageButton(page) {
        const button = document.createElement('button');
        button.classList.add('pagination-number');
        button.textContent = page + 1;

        if (page === currentPage) {
            button.classList.add('active');
        }

        button.addEventListener('click', () => {
            currentPage = page;
            loadCourses();
            updateActivePageButton();
        });

        paginationNumbers.appendChild(button);
    }

    /**
     * Translate the language string into an emoji flag.
     *
     * @param {string} lang The language short name.
     * @returns {string} The emoji flag representing the language.
     */
    function selectLangFlag(lang) {
        switch (lang) {
            case 'pt_br': return '🇧🇷';
            case 'en': return '🇺🇸';
            case 'es': return '🇪🇸';
            default: return '🌐';
        }
    }

    /**
     * Update active class on current page pagination button.
     *
     * @returns {void}
     */
    function updateActivePageButton() {
        document.querySelectorAll('.pagination-number').forEach(button => button.classList.remove('active'));
        const activeButton = document.querySelector(`.pagination-number:nth-child(${currentPage + 1})`);
        if (activeButton) {
            activeButton.classList.add('active');
        }
    }

    /**
     * Get selected filters from DOM.
     *
     * @returns {object} The filter parameters.
     */
    function getFilter() {
        const filters = { workload: [], certificate: [], lang: [], learningpath: [] };

        const slider = document.getElementById('workload-slider');
        if (slider && slider.noUiSlider) {
            const values = slider.noUiSlider.get(); // retorna como ["20", "60"]
            const [minValue, maxValue] = values.map(Number); // transforma em [20, 60]
            const range = slider.noUiSlider.options.range;
            const minPossible = range.min;
            const maxPossible = range.max;

            if (minValue === minPossible && maxValue === maxPossible) {
                filters.workload = [0];
            } else {
                filters.workload = [minValue, maxValue];
            }
        }

        filters.workload = filters.workload.join(',');

        document.querySelectorAll('#filter-content-certificate-column input[type="checkbox"]:checked')
            .forEach(checkbox => filters.certificate.push(checkbox.value));
        filters.certificate = filters.certificate.join(',');

        document.querySelectorAll('#filter-content-lang-column input[type="checkbox"]:checked')
            .forEach(checkbox => filters.lang.push(checkbox.value));
        filters.lang = filters.lang.join(',');

        document.querySelectorAll('#filter-content-learningpath-column input[type="checkbox"]:checked')
            .forEach(checkbox => filters.learningpath.push(checkbox.value));
        filters.learningpath = filters.learningpath.join(',');

        return filters;
    }

    /**
     * Update the visual count badge on the filter button.
     *
     * @returns {void}
     */
    function updateFilterBadge() {
        const filters = getFilter();
        let totalFilters = 0;

        // Verifica o filtro de carga horária.
        if (filters.workload && filters.workload !== '0') {
            totalFilters++;
        }

        // Verifica checkboxes de certificado, idioma e trilha.
        ['certificate', 'lang', 'learningpath'].forEach(key => {
            if (filters[key]) {
                const values = filters[key].split(',').filter(Boolean); // ignora strings vazias
                totalFilters += values.length;
            }
        });

        const badge = document.querySelector('.filter-badge');
        badge.style.display = totalFilters > 0 ? 'inline-block' : 'none';
        badge.innerHTML = totalFilters > 0 ? totalFilters : '';
    }

    /**
     * Close the filter modal.
     *
     * @returns {void}
     */
    function closeFilter() {
        document.querySelector('#filter-area').style.display = 'none';
        document.querySelector('#modal-overlay').style.display = 'none';
        toggleScroll();
    }

    /**
     * Set main page wrapper margins to zero.
     *
     * @returns {void}
     */
    function correctMainPadding() {
        const main = document.querySelector('[role="main"]');
        main.style.paddingLeft = '0';
        main.style.paddingRight = '0';
    }

    /**
     * Toggle overflow property on body to enable/disable scrolling.
     *
     * @returns {void}
     */
    function toggleScroll() {
        const body = document.querySelector('body');
        if (body.style.overflow === 'hidden') {
            body.style.overflow = 'auto';
        } else {
            body.style.overflow = 'hidden';
        }
    }

    searchInput.addEventListener('input', loadCourses);

    prevPageButton.addEventListener('click', () => {
        if (currentPage > 0) {
            currentPage--;
            loadCourses();
        }
    });

    nextPageButton.addEventListener('click', () => {
        if ((currentPage + 1) * limit < totalCourses) {
            currentPage++;
            loadCourses();
        }
    });

    document.querySelector('#filter-courses').addEventListener('click', () => {
        document.querySelector('#filter-area').style.display = 'block';
        document.querySelector('#modal-overlay').style.display = 'block';
        toggleScroll();
    });

    document.querySelector('#clear-filter').addEventListener('click', () => {
        document.querySelectorAll('.filter-content input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Reseta o slider (noUiSlider).
        const slider = document.getElementById('workload-slider');
        if (slider && slider.noUiSlider) {
            const range = slider.noUiSlider.options.range;
            slider.noUiSlider.set([range.min, range.max]);
        }

        updateFilterBadge();

        currentPage = 0;
        loadCourses();
    });

    document.querySelector('#apply-filter').addEventListener('click', () => {
        currentPage = 0;
        loadCourses();
        closeFilter();
        updateFilterBadge();
    });

    document.querySelector('#close-filter').addEventListener('click', closeFilter);

    document.querySelector('#modal-overlay').addEventListener('click', closeFilter);

    window.addEventListener('load', correctMainPadding);

    return {
        init: (requestUrl, maxCourses) => {
            url = requestUrl;
            limit = maxCourses;
            limit = limit || 9;
            loadCourses();
        }
    };
});