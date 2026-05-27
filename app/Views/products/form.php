<?php
$isEdit = $mode === 'edit' && $product !== null;
$formAction = $isEdit ? '/produits/' . $product->id . '/modifier' : '/produits';
$formId = $isEdit ? 'product-edit-' . $product->id : 'product-create';
$priceValue = $isEdit
    ? number_format($product->priceCents / 100, 2, '.', '')
    : (string) old('price');
$serviceFeePreview = $pricing->formatCents($pricing->serviceFeeCents((int) round(((float) str_replace(',', '.', $priceValue ?: '0')) * 100)));
?>
<section class="panel wide-panel">
    <p class="eyebrow"><?= $isEdit ? 'Modifier un article' : 'Nouvelle annonce' ?></p>
    <h1><?= $isEdit ? 'Mettre à jour le produit' : 'Ajouter un produit à vendre' ?></h1>
    <form method="post" action="<?= e(url($formAction)) ?>" enctype="multipart/form-data" class="stack-form">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token($formId)) ?>">

        <label for="name">Nom du produit</label>
        <input id="name" name="name" type="text" required maxlength="120" value="<?= e($isEdit ? $product->name : (string) old('name')) ?>">

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="6" required><?= e($isEdit ? $product->description : (string) old('description')) ?></textarea>

        <label for="price">Prix affiché (CAD)</label>
        <input id="price" name="price" type="text" required inputmode="decimal" value="<?= e($priceValue) ?>">
        <p class="muted">Frais de service estimés pour ce prix: <strong><?= e($serviceFeePreview) ?></strong></p>

        <label for="image">Image du produit <?= $isEdit ? '(laisser vide pour conserver l’image actuelle)' : '' ?></label>
        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png" <?= $isEdit ? '' : 'required' ?>>

        <?php if ($isEdit): ?>
            <img src="<?= e(url($product->imagePath)) ?>" alt="<?= e($product->name) ?>" class="form-preview-image">
        <?php endif; ?>

        <button class="button button-primary" type="submit"><?= $isEdit ? 'Enregistrer les changements' : 'Publier le produit' ?></button>
    </form>
</section>
