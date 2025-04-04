document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            let tabName = this.getAttribute('data-tab');

            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));
            this.classList.add('nav-tab-active');

            document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
            document.getElementById(tabName).style.display = 'block';
        });
    });

    document.querySelector('.messenger-settings-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save_messenger_settings');
        formData.append('nonce', messengerNotifierAjax.nonce);
        formData.append('form_data', new URLSearchParams([...formData]).toString());

        fetch(messengerNotifierAjax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const notice = document.getElementById('settings-notice');
            notice.textContent = data.message;
            notice.className = data.status === 'success' ? 'success' : 'error';
        });
    });
});
