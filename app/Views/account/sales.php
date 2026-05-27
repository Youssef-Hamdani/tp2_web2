<section class="panel wide-panel">
    <p class="eyebrow">Historique</p>
    <h1>Mes ventes</h1>
    <?php if ($orders === []): ?>
        <p>Aucune vente pour le moment.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
            <tr>
                <th>Produit</th>
                <th>Prix avant taxes et frais</th>
                <th>Date de la vente</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= e($order->productName) ?></td>
                    <td><?= e($pricing->formatCents($order->subtotalCents)) ?></td>
                    <td><?= e((string) $order->purchasedAt) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
