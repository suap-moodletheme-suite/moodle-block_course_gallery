define(['jquery', 'https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js'], function($, noUiSlider) {
    return {
        /**
         * Initialize the noUiSlider widget.
         *
         * @returns {void}
         */
        init: function() {
            const slider = document.getElementById('workload-slider');
            if (!slider) {
                return;
            }

            const rangeDisplay = document.getElementById('workload-range-display');
            const rangeTotal = document.getElementById('workload-range-total');

            noUiSlider.create(slider, {
                start: [10, 100],
                connect: true,
                range: {
                    'min': 10,
                    'max': 100
                },
                step: 10,
                format: {
                    to: value => Math.round(value),
                    from: value => Number(value)
                }
            });

            const minPossible = slider.noUiSlider.options.range.min;
            const maxPossible = slider.noUiSlider.options.range.max;
            rangeTotal.innerText = `${minPossible}h - ${maxPossible}h`;

            slider.noUiSlider.on('update', function(values) {
                rangeDisplay.textContent = `${values[0]}h – ${values[1]}h`;
            });
        }
    };
});
