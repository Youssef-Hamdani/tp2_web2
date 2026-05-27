document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('checkout-form');
    if (!form) {
        return;
    }

    const sameAsBilling = document.getElementById('same_as_billing');
    const message = document.getElementById('checkout-message');
    const stripeKey = form.dataset.stripeKey || '';

    const billingFields = {
        address: document.getElementById('billing_address_line1'),
        address2: document.getElementById('billing_address_line2'),
        city: document.getElementById('billing_city'),
        province: document.getElementById('billing_province'),
        postalCode: document.getElementById('billing_postal_code'),
    };

    const shippingFields = {
        address: document.getElementById('shipping_address_line1'),
        address2: document.getElementById('shipping_address_line2'),
        city: document.getElementById('shipping_city'),
        province: document.getElementById('shipping_province'),
        postalCode: document.getElementById('shipping_postal_code'),
    };

    function syncShippingFields() {
        if (!sameAsBilling.checked) {
            return;
        }

        shippingFields.address.value = billingFields.address.value;
        shippingFields.address2.value = billingFields.address2.value;
        shippingFields.city.value = billingFields.city.value;
        shippingFields.province.value = billingFields.province.value;
        shippingFields.postalCode.value = billingFields.postalCode.value;
    }

    function toggleShippingFields() {
        const disabled = sameAsBilling.checked;
        const container = document.getElementById('shipping-fields');

        Object.values(shippingFields).forEach((field) => {
            field.readOnly = disabled;
        });

        if (disabled) {
            syncShippingFields();
            container.classList.add('is-muted');
        } else {
            container.classList.remove('is-muted');
        }
    }

    sameAsBilling.addEventListener('change', toggleShippingFields);
    Object.values(billingFields).forEach((field) => field.addEventListener('input', syncShippingFields));
    toggleShippingFields();

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        message.innerText = 'Préparation du paiement...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await response.json();
            if (!response.ok) {
                message.innerText = data.message || 'Une erreur est survenue.';
                return;
            }

            const stripe = Stripe(stripeKey);
            const result = await stripe.redirectToCheckout({ sessionId: data.id });

            if (result.error) {
                message.innerText = result.error.message || 'Impossible de rediriger vers Stripe.';
            }
        } catch (error) {
            message.innerText = 'Le service de paiement est temporairement indisponible.';
        }
    });
});
