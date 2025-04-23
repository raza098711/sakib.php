document.addEventListener('DOMContentLoaded', function () {
    // DOM Elements
    const elements = {
        orderForm: document.getElementById('orderForm'),
        cakeOrderForm: document.getElementById('cakeOrderForm'),
        cakeNameInput: document.getElementById('cakeName'),
        cakeIdInput: document.getElementById('cakeId'),
        closeFormBtn: document.getElementById('closeFormBtn'),
        cancelOrderBtn: document.getElementById('cancelOrderBtn'),
        orderConfirmation: document.getElementById('orderConfirmation'),
        confirmationMessage: document.getElementById('confirmationMessage'),
        confirmCloseBtn: document.getElementById('confirmCloseBtn'),
        closeModalBtns: document.querySelectorAll('.modal .close')
    };

    // Audio for notifications
    const orderSound = new Audio('https://www.soundjay.com/buttons/beep-01a.mp3');
    const quotationSound = new Audio('https://www.soundjay.com/buttons/beep-02.mp3');

    // Smooth scrolling for navigation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
                document.querySelectorAll('nav ul li a').forEach(link => link.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // Apply animations
    applyAnimations();

    // Event Listeners
    if (elements.closeFormBtn) {
        elements.closeFormBtn.addEventListener('click', () => {
            elements.orderForm.classList.add('hidden');
        });
    }

    if (elements.cancelOrderBtn) {
        elements.cancelOrderBtn.addEventListener('click', () => {
            elements.orderForm.classList.add('hidden');
            elements.cakeOrderForm.reset();
        });
    }

    if (elements.confirmCloseBtn) {
        elements.confirmCloseBtn.addEventListener('click', () => {
            elements.orderConfirmation.classList.add('hidden');
        });
    }

    elements.closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('.modal').classList.add('hidden');
        });
    });

    // Order form trigger
    document.querySelectorAll('.order-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const cakeName = e.target.getAttribute('data-cake');
            const cakeId = e.target.getAttribute('data-cake-id');
            elements.cakeNameInput.value = cakeName;
            elements.cakeIdInput.value = cakeId;
            elements.orderForm.classList.remove('hidden');
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('deliveryDate').min = tomorrow.toISOString().split('T')[0];
        });
    });

    // Handle confirmation message
    <? php if (isset($_SESSION['confirmation_message'])): ?>
        elements.orderConfirmation.classList.remove('hidden');
    elements.confirmationMessage.textContent = '<?php echo addslashes($_SESSION['confirmation_message']); ?>';
        <? php if ($_SESSION['notification_type'] === 'quotation'): ?>
        quotationSound.play().catch(e => console.error('Quotation sound error:', e));
        <? php else: ?>
        orderSound.play().catch(e => console.error('Order sound error:', e));
        <? php endif; ?>
        <? php unset($_SESSION['confirmation_message'], $_SESSION['notification_type']); ?>
    <? php endif; ?>

        // Functions
        function applyAnimations() {
            const elementsToAnimate = document.querySelectorAll('.cake-card, .form-container, .modal-content');
            elementsToAnimate.forEach(el => {
                el.classList.remove('animate-fade');
                void el.offsetWidth; // Trigger reflow
                el.classList.add('animate-fade');
            });
        }
});