<style>
    form.is-submitting button[type="submit"] {
        position:relative;
        overflow: hidden;
        outline: 0;
    }

    form.is-submitting button[type="submit"]::before {
        position: absolute;
        content: '';
        height: 0.2em;
        left: 0;
        right: 0;
        top: 0;
        background-color: rgba(49, 46, 129, 0.9);
        animation: move 1s linear infinite;
    }

    @keyframes move {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }
</style>


    <script>

        // Defer initiation when dom is ready
        document.addEventListener('DOMContentLoaded', function(){

            // Prevent Double Submits
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    // Prevent if already submitting
                    if (form.classList.contains('is-submitting')) {
                        e.preventDefault();
                    }

                    // Add class to hook our visual indicator on
                    form.classList.add('is-submitting');
                });
            });
        });
    </script>
