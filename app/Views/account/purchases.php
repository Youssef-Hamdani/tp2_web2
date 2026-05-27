<section class="panel wide-panel">
    <p class="eyebrow">Historique</p>
    <h1>Mes achats</h1>
    <?php if ($orders === []): ?>
        <p>Aucun achat pour le moment.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
            <tr>
                <th>Produit</th>
                <th>Prix après taxes et frais</th>
                <th>Date de l’achat</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= e($order->productName) ?></td>
                    <td><?= e($pricing->formatCents($order->totalCents)) ?></td>
                    <td><?= e((string) $order->purchasedAt) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
