-- Sample categories and products for local development.
-- Usage: mysql -u root -padmin123 < database/seed.sql
-- Products with variants use the lowest variant price as their base price.

USE daily_spot_cafe;

-- Categories ---------------------------------------------------------------
INSERT INTO categories (category_name, description, is_active, has_variants, has_sizes) VALUES
    ('Coffee',     'Espresso-based drinks made with locally roasted beans.', 1, 1, 1);
SET @coffee = LAST_INSERT_ID();
INSERT INTO categories (category_name, description, is_active, has_variants, has_sizes) VALUES
    ('Non-Coffee', 'Chocolate, matcha and other caffeine-light favorites.',  1, 1, 1);
SET @noncoffee = LAST_INSERT_ID();
INSERT INTO categories (category_name, description, is_active, has_variants, has_sizes) VALUES
    ('Frappe',     'Blended iced drinks topped with whipped cream.',         1, 0, 1);
SET @frappe = LAST_INSERT_ID();
INSERT INTO categories (category_name, description, is_active, has_variants, has_sizes) VALUES
    ('Pastries',   'Freshly baked every morning.',                           1, 0, 0);
SET @pastries = LAST_INSERT_ID();
INSERT INTO categories (category_name, description, is_active, has_variants, has_sizes) VALUES
    ('All-Day Meals', 'Filipino and Western plates served all day.',         1, 0, 0);
SET @meals = LAST_INSERT_ID();

-- Coffee (Hot/Iced x Grande/Venti) ------------------------------------------
INSERT INTO products (product_name, price, short_description, long_description, category_id) VALUES
    ('Americano', 110, 'Espresso with hot water for a clean, bold cup.',
     'Two shots of our house espresso topped with water. Simple, strong and smooth.', @coffee);
SET @p = LAST_INSERT_ID();
INSERT INTO product_variants (product_id, variant_type, size_label, price) VALUES
    (@p, 'Hot', 'Grande', 110), (@p, 'Hot', 'Venti', 130), (@p, 'Iced', 'Grande', 120), (@p, 'Iced', 'Venti', 140);

INSERT INTO products (product_name, price, short_description, long_description, category_id) VALUES
    ('Cafe Latte', 140, 'Espresso with steamed milk and light foam.',
     'A creamy classic made with double espresso and fresh steamed milk.', @coffee);
SET @p = LAST_INSERT_ID();
INSERT INTO product_variants (product_id, variant_type, size_label, price) VALUES
    (@p, 'Hot', 'Grande', 140), (@p, 'Hot', 'Venti', 160), (@p, 'Iced', 'Grande', 150), (@p, 'Iced', 'Venti', 170);

INSERT INTO products (product_name, price, short_description, long_description, category_id) VALUES
    ('Caramel Macchiato', 160, 'Vanilla, milk and espresso finished with caramel drizzle.',
     'Layered vanilla syrup, steamed milk and espresso, finished with a buttery caramel drizzle.', @coffee);
SET @p = LAST_INSERT_ID();
INSERT INTO product_variants (product_id, variant_type, size_label, price) VALUES
    (@p, 'Hot', 'Grande', 160), (@p, 'Hot', 'Venti', 180), (@p, 'Iced', 'Grande', 170), (@p, 'Iced', 'Venti', 190);

INSERT INTO products (product_name, price, short_description, long_description, category_id) VALUES
    ('Spanish Latte', 155, 'Sweet and creamy latte with condensed milk.',
     'Espresso and milk sweetened with condensed milk. A local favorite.', @coffee);
SET @p = LAST_INSERT_ID();
INSERT INTO product_variants (product_id, variant_type, size_label, price) VALUES
    (@p, 'Hot', 'Grande', 155), (@p, 'Hot', 'Venti', 175), (@p, 'Iced', 'Grande', 165), (@p, 'Iced', 'Venti', 185);

-- Non-Coffee (Hot/Iced x Grande/Venti) --------------------------------------
INSERT INTO products (product_name, price, short_description, long_description, category_id) VALUES
    ('Matcha Latte', 150, 'Ceremonial-grade matcha whisked with milk.',
     'Earthy Japanese matcha balanced with fresh milk and a touch of sweetness.', @noncoffee);
SET @p = LAST_INSERT_ID();
INSERT INTO product_variants (product_id, variant_type, size_label, price) VALUES
    (@p, 'Hot', 'Grande', 150), (@p, 'Hot', 'Venti', 170), (@p, 'Iced', 'Grande', 160), (@p, 'Iced', 'Venti', 180);

INSERT INTO products (product_name, price, short_description, long_description, category_id) VALUES
    ('Tablea Chocolate', 130, 'Rich chocolate made from native cacao tablea.',
     'Made with Batangas tablea for a thick, deeply chocolatey drink.', @noncoffee);
SET @p = LAST_INSERT_ID();
INSERT INTO product_variants (product_id, variant_type, size_label, price) VALUES
    (@p, 'Hot', 'Grande', 130), (@p, 'Hot', 'Venti', 150), (@p, 'Iced', 'Grande', 140), (@p, 'Iced', 'Venti', 160);

-- Frappe (Grande/Venti only) ------------------------------------------------
INSERT INTO products (product_name, price, short_description, long_description, category_id) VALUES
    ('Java Chip Frappe', 175, 'Coffee, chocolate chips and milk blended with ice.',
     'Blended coffee with mocha sauce and chocolate chips, topped with whipped cream.', @frappe);
SET @p = LAST_INSERT_ID();
INSERT INTO product_variants (product_id, variant_type, size_label, price) VALUES
    (@p, NULL, 'Grande', 175), (@p, NULL, 'Venti', 195);

INSERT INTO products (product_name, price, short_description, long_description, category_id) VALUES
    ('Strawberry Cream Frappe', 170, 'Strawberry puree blended with milk and ice.',
     'A caffeine-free blend of sweet strawberries and cream, topped with whipped cream.', @frappe);
SET @p = LAST_INSERT_ID();
INSERT INTO product_variants (product_id, variant_type, size_label, price) VALUES
    (@p, NULL, 'Grande', 170), (@p, NULL, 'Venti', 190);

-- Pastries (single price) ---------------------------------------------------
INSERT INTO products (product_name, price, short_description, long_description, category_id) VALUES
    ('Butter Croissant', 95,  'Flaky, buttery and baked fresh daily.',
     'Laminated dough baked until golden with a soft, airy center.', @pastries),
    ('Ensaymada',        75,  'Soft brioche topped with butter, sugar and cheese.',
     'A Filipino classic: pillowy bread with buttercream, sugar and grated cheese.', @pastries),
    ('Banana Bread',     85,  'Moist loaf slice with toasted walnuts.',
     'Made with ripe bananas and walnuts. Great with a hot Americano.', @pastries),
    ('Blueberry Cheesecake', 165, 'Creamy baked cheesecake with blueberry topping.',
     'New York-style cheesecake on a graham crust, finished with blueberry compote.', @pastries);

-- All-Day Meals (single price) ----------------------------------------------
INSERT INTO products (product_name, price, short_description, long_description, category_id) VALUES
    ('Tapsilog',            220, 'Beef tapa, garlic rice and fried egg.',
     'Sweet-savory cured beef served with garlic fried rice, sunny-side-up egg and vinegar dip.', @meals),
    ('Chicken Pesto Pasta', 245, 'Penne in basil pesto with grilled chicken.',
     'Al dente penne tossed in basil pesto with grilled chicken and parmesan.', @meals),
    ('Clubhouse Sandwich',  210, 'Triple-decker with chicken, ham, egg and fries.',
     'Toasted bread layered with chicken, ham, egg, lettuce and tomato, served with fries.', @meals);
