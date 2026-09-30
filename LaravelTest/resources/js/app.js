document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('textarea[name="body"]')

    forms.forEach((textarea) => {
        const form = textarea.closest('form')
        const counter = form?.querySelector('[data-comment-count]')

        if (!counter) {
            return
        }

        const max = Number(textarea.getAttribute('maxlength')) || 0
        const min = Number(textarea.getAttribute('minlength')) || 0
        const submit = form.querySelector('button[type="submit"]')

        const sync = () => {
            const length = textarea.value.trim().length

            counter.textContent = String(length)

            if (max > 0 && length > max * 0.9) {
                counter.classList.add('text-amber-400')
            } else {
                counter.classList.remove('text-amber-400')
            }

            if (submit) {
                submit.disabled = length < min
            }
        }

        textarea.addEventListener('input', sync)
        sync()
    })
})
