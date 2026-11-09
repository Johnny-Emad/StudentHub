import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global toast notification store
Alpine.store('toast', {
    message: '',
    type: 'success',
    visible: false,
    show(message, type = 'success') {
        this.message = message;
        this.type = type;
        this.visible = true;
        setTimeout(() => { this.visible = false; }, 3500);
    }
});

Alpine.start();
