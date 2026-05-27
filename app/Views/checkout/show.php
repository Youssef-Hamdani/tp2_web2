<?php $summary = $pricing->summary($product->priceCents); ?>
<section class="checkout-grid">
    <article class="panel">
        <p class="eyebrow">Paiement</p>
        <h1>Finaliser l’achat</h1>
        <p>Le paiement de la carte se fera sur Stripe après la validation de ce formulaire.</p>

        <?php if (!$stripeConfigured): ?>
            <div class="alert alert-error" role="alert">Stripe n’est pas encore configuré. Ajoutez les clés Stripe pour activer le paiement réel.</div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/commande/session-stripe')) ?>" id="checkout-form" class="stack-form" data-stripe-key="<?= e($stripeKey) ?>">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token('checkout')) ?>">

            <h2>Informations personnelles</h2>
            <div class="two-columns">
                <div>
                    <label for="buyer_first_name">Prénom</label>
                    <input id="buyer_first_name" name="buyer_first_name" type="text" required>
                </div>
                <div>
                    <label for="buyer_last_name">Nom</label>
                    <input id="buyer_last_name" name="buyer_last_name" type="text" required>
                </div>
            </div>

            <h2>Adresse de facturation</h2>
            <label for="billing_address_line1">Adresse</label>
            <input id="billing_address_line1" name="billing_address_line1" type="text" required>

            <label for="billing_address_line2">Complément d’adresse</label>
            <input id="billing_address_line2" name="billing_address_line2" type="text">

            <div class="three-columns">
                <div>
                    <label for="billing_city">Ville</label>
                    <input id="billing_city" name="billing_city" type="text" required>
                </div>
                <div>
                    <label for="billing_province">Province</label>
                    <input id="billing_province" name="billing_province" type="text" required value="QC">
                </div>
                <div>
                    <label for="billing_postal_code">Code postal</label>
                    <input id="billing_postal_code" name="billing_postal_code" type="text" required>
                </div>
            </div>

            <label class="checkbox-row">
                <input id="same_as_billing" type="checkbox" checked>
                <span>Utiliser la même adresse pour la livraison</span>
            </label>

            <div id="shipping-fields">
                <h2>Adresse de livraison</h2>
                <label for="shipping_address_line1">Adresse</label>
                <input id="shipping_address_line1" name="shipping_address_line1" type="text" required>

                <label for="shipping_address_line2">Complément d’adresse</label>
                <input id="shipping_address_line2" name="shipping_address_line2" type="text">

                <div class="three-columns">
                    <div>
                        <label for="shipping_city">Ville</label>
                        <input id="shipping_city" name="shipping_city" type="text" required>
                    </div>
                    <div>
                        <label for="shipping_province">Province</label>
                        <input id="shipping_province" name="shipping_province" type="text" required value="QC">
                    </div>
                    <div>
                        <label for="shipping_postal_code">Code postal</label>
                        <input id="shipping_postal_code" name="shipping_postal_code" type="text" required>
                    </div>
                </div>
            </div>

            <p class="muted">Les renseignements de carte seront saisis directement sur la page sécurisée de Stripe après cette étape.</p>
            <p id="checkout-message" class="checkout-message" aria-live="polite"></p>
            <button class="button button-primary" type="submit" <?= $stripeConfigured ? '' : 'disabled' ?>>Continuer vers Stripe</button>
        </form>
    </article>

    <aside class="panel">
        <h2>Résumé de la commande</h2>
        <img src="<?= e(url($product->imagePath)) ?>" alt="<?= e($product->name) ?>" class="product-image">
        <p><strong><?= e($product->name) ?></strong></p>
        <dl class="summary-table">
            <div><dt>Sous-total</dt><dd><?= e($pricing->formatCents($summary['subtotal_cents'])) ?></dd></div>
            <div><dt>Frais de service</dt><dd><?= e($pricing->formatCents($summary['service_fee_cents'])) ?></dd></div>
            <div><dt>TPS</dt><dd><?= e($pricing->formatCents($summary['gst_cents'])) ?></dd></div>
            <div><dt>TVQ</dt><dd><?= e($pricing->formatCents($summary['qst_cents'])) ?></dd></div>
            <div class="summary-total"><dt>Total</dt><dd><?= e($pricing->formatCents($summary['total_cents'])) ?></dd></div>
        </dl>
    </aside>
</section>

<script src="https://js.stripe.com/v3/"></script>
<script src="<?= e(asset_url('assets/js/checkout.js')) ?>" defer></script>
