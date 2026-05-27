SET NAMES utf8mb4;

DROP TABLE IF EXISTS sales_logs;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'member',
    is_active TINYINT(1) NOT NULL DEFAULT 0,
    activation_token_hash VARCHAR(255) NULL,
    activation_expires_at DATETIME NULL,
    reset_token_hash VARCHAR(255) NULL,
    reset_expires_at DATETIME NULL,
    remember_token_hash VARCHAR(255) NULL,
    remember_expires_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seller_user_id INT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    price_cents INT UNSIGNED NOT NULL,
    service_fee_cents INT UNSIGNED NOT NULL,
    status ENUM('available', 'sold') NOT NULL DEFAULT 'available',
    sold_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_products_seller FOREIGN KEY (seller_user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    buyer_user_id INT UNSIGNED NOT NULL,
    seller_user_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    product_name VARCHAR(120) NOT NULL,
    subtotal_cents INT UNSIGNED NOT NULL,
    service_fee_cents INT UNSIGNED NOT NULL,
    gst_cents INT UNSIGNED NOT NULL,
    qst_cents INT UNSIGNED NOT NULL,
    total_cents INT UNSIGNED NOT NULL,
    buyer_first_name VARCHAR(120) NOT NULL,
    buyer_last_name VARCHAR(120) NOT NULL,
    billing_address_line1 VARCHAR(190) NOT NULL,
    billing_address_line2 VARCHAR(190) NULL,
    billing_city VARCHAR(120) NOT NULL,
    billing_province VARCHAR(30) NOT NULL,
    billing_postal_code VARCHAR(12) NOT NULL,
    shipping_address_line1 VARCHAR(190) NOT NULL,
    shipping_address_line2 VARCHAR(190) NULL,
    shipping_city VARCHAR(120) NOT NULL,
    shipping_province VARCHAR(30) NOT NULL,
    shipping_postal_code VARCHAR(12) NOT NULL,
    stripe_session_id VARCHAR(255) NULL,
    stripe_payment_intent_id VARCHAR(255) NULL,
    stripe_status VARCHAR(50) NOT NULL DEFAULT 'pending',
    status ENUM('pending', 'paid', 'cancelled') NOT NULL DEFAULT 'pending',
    payment_payload JSON NULL,
    purchased_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_orders_buyer FOREIGN KEY (buyer_user_id) REFERENCES users(id),
    CONSTRAINT fk_orders_seller FOREIGN KEY (seller_user_id) REFERENCES users(id),
    CONSTRAINT fk_orders_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sales_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    payload_json JSON NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_sales_logs_order FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (
    id, email, password_hash, role, is_active, activation_token_hash, activation_expires_at,
    reset_token_hash, reset_expires_at, remember_token_hash, remember_expires_at, created_at, updated_at
) VALUES
    (1, 'acheteur@example.com', '$2b$12$ksx2GoNtn7WpjpMHngoI1.XSzj4I1d/a2OM/5DDyzsPMqTGtHUQbC', 'member', 1, NULL, NULL, NULL, NULL, NULL, NULL, NOW(), NOW()),
    (2, 'vendeur@example.com', '$2b$12$KtBhB3BQw5Sielg1KAdQvuOgoK6UiEPsVe8T6N73W///.wfU560b2', 'member', 1, NULL, NULL, NULL, NULL, NULL, NULL, NOW(), NOW()),
    (3, 'membre@example.com', '$2b$12$YG86/KSLnf4C00Z9iFSOF.DNSpvSbZr832W1/dmEVhIuTL3W5iMI2', 'member', 0, SHA2('activation-seed', 256), DATE_ADD(NOW(), INTERVAL 1 HOUR), NULL, NULL, NULL, NULL, NOW(), NOW());

INSERT INTO products (
    id, seller_user_id, name, description, image_path, price_cents, service_fee_cents, status, sold_at, created_at, updated_at
) VALUES
    (1, 2, 'Vélo urbain bleu', 'Vélo de ville en bon état, parfait pour les trajets quotidiens.', '/assets/demo/velo.svg', 32500, 973, 'available', NULL, NOW(), NOW()),
    (2, 3, 'Chaise vintage en bois', 'Chaise restaurée à la main, très confortable pour un bureau ou une salle à manger.', '/assets/demo/chaise.svg', 9800, 315, 'available', NULL, NOW(), NOW()),
    (3, 2, 'Lampe artisanale', 'Lampe décorative vendue lors d’une transaction de démonstration.', '/assets/demo/lampe.svg', 15000, 465, 'sold', NOW(), NOW(), NOW());

INSERT INTO orders (
    id, buyer_user_id, seller_user_id, product_id, product_name,
    subtotal_cents, service_fee_cents, gst_cents, qst_cents, total_cents,
    buyer_first_name, buyer_last_name,
    billing_address_line1, billing_address_line2, billing_city, billing_province, billing_postal_code,
    shipping_address_line1, shipping_address_line2, shipping_city, shipping_province, shipping_postal_code,
    stripe_session_id, stripe_payment_intent_id, stripe_status, status, payment_payload, purchased_at, created_at, updated_at
) VALUES (
    1, 1, 2, 3, 'Lampe artisanale',
    15000, 465, 750, 1496, 17711,
    'Alice', 'Acheteuse',
    '123 Rue des Pins', '', 'Joliette', 'QC', 'J6E1A1',
    '123 Rue des Pins', '', 'Joliette', 'QC', 'J6E1A1',
    'cs_test_seed', 'pi_seed', 'succeeded', 'paid',
    JSON_OBJECT('source', 'seed', 'note', 'Commande de démonstration'),
    NOW(), NOW(), NOW()
);

INSERT INTO sales_logs (order_id, payload_json, created_at)
VALUES (
    1,
    JSON_OBJECT(
        'user_id', 1,
        'order_id', 1,
        'product_id', 3,
        'checkout_session', 'cs_test_seed',
        'payment_status', 'paid',
        'payment_intent_id', 'pi_seed',
        'payment_intent_status', 'succeeded',
        'total_cents', 17711
    ),
    NOW()
);
