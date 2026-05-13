<?php
/**
 * Catalog of 10 products/services for Sweet Crumb Homemade Cookies.
 * Keys are product IDs (1-10).
 */
function cookie_catalog_base_url(): string
{
    $scheme = 'http';
    $forwardedProto = trim((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    if ($forwardedProto !== '') {
        $scheme = strtolower(explode(',', $forwardedProto)[0]) === 'https' ? 'https' : 'http';
    } elseif (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        $scheme = 'https';
    } elseif ((int) ($_SERVER['SERVER_PORT'] ?? 80) === 443) {
        $scheme = 'https';
    }

    $host = trim((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/'));
    $basePath = trim(dirname($scriptName), '/');

    return $basePath === ''
        ? $scheme . '://' . $host
        : $scheme . '://' . $host . '/' . $basePath;
}

function cookie_catalog_image_url(string $slug): string
{
    return cookie_catalog_base_url() . '/product_image.php?slug=' . rawurlencode($slug);
}

function get_products_catalog(): array
{
    $catalog = [
        1 => [
            'slug'        => 'classic-chocolate-chip',
            'name'        => 'Classic Chocolate Chip',
            'short'       => 'Soft-baked vanilla dough packed with semisweet chips.',
            'description' => 'Our signature cookie is baked with brown sugar, vanilla, and generous semisweet chocolate chips for crisp edges and a soft, gooey center. It is the crowd favorite for gift boxes, office trays, and late-night dessert cravings.',
        ],
        2 => [
            'slug'        => 'oatmeal-raisin-deluxe',
            'name'        => 'Oatmeal Raisin Deluxe',
            'short'       => 'Chewy rolled oats, golden raisins, and warm cinnamon.',
            'description' => 'This home-style oatmeal cookie layers toasted oats, plump raisins, and a gentle cinnamon finish for a hearty bite that feels classic and comforting. It is slightly less sweet than our chocolate varieties, which makes it popular for breakfast meetings and afternoon coffee breaks.',
        ],
        3 => [
            'slug'        => 'decorated-sugar-cookies',
            'name'        => 'Decorated Sugar Cookies',
            'short'       => 'Vanilla cut-out cookies finished with custom royal icing.',
            'description' => 'Our decorated sugar cookies are buttery vanilla cut-outs topped with hand-piped royal icing in themed colors and simple event designs. They work especially well for birthdays, baby showers, holiday gifting, and branded dessert tables with advance notice.',
        ],
        4 => [
            'slug'        => 'snickerdoodle',
            'name'        => 'Snickerdoodle',
            'short'       => 'Tender cinnamon-sugar cookie with a crackly top.',
            'description' => 'Cream of tartar gives this cookie its classic tang while a cinnamon-sugar coating bakes into a lightly crisp shell. The center stays soft and pillowy, making it a great choice for anyone who wants something cozy and not overly rich.',
        ],
        5 => [
            'slug'        => 'double-chocolate-fudge',
            'name'        => 'Double Chocolate Fudge',
            'short'       => 'Rich cocoa cookie loaded with dark chocolate chunks.',
            'description' => 'Built for serious chocolate lovers, this deep cocoa cookie bakes up like a soft brownie with extra dark chocolate folded into every batch. It is dense, fudgy, and bold enough to pair well with espresso, cold brew, or vanilla ice cream.',
        ],
        6 => [
            'slug'        => 'lemon-glazed-shortbread',
            'name'        => 'Lemon Glazed Shortbread',
            'short'       => 'Buttery shortbread brightened with lemon zest and glaze.',
            'description' => 'This crisp shortbread cookie is made with fresh lemon zest and finished with a smooth citrus glaze for a bright, clean flavor. It is one of our lightest cookies and works especially well for spring menus, brunches, and tea-time assortments.',
        ],
        7 => [
            'slug'        => 'peanut-butter-blossom',
            'name'        => 'Peanut Butter Blossom',
            'short'       => 'Sugar-rolled peanut butter cookie with a chocolate center.',
            'description' => 'The peanut butter blossom starts with a soft peanut butter dough rolled in sugar and ends with a chocolate center pressed in while warm. It is sweet, nutty, and nostalgic, with the perfect mix of peanut butter richness and milk chocolate on top.',
        ],
        8 => [
            'slug'        => 'custom-catering-tray',
            'name'        => 'Custom Catering Tray',
            'short'       => 'A large assorted tray of customer favorites for events.',
            'description' => 'Our catering tray includes a mix of bestsellers such as chocolate chip, snickerdoodle, decorated sugar cookies, and seasonal specials, arranged for easy sharing. It is designed for office meetings, birthdays, and celebrations, and we can label common allergens on request.',
        ],
        9 => [
            'slug'        => 'gift-box-subscription',
            'name'        => 'Gift Box Subscription',
            'short'       => 'Monthly cookie box with rotating flavors and baker picks.',
            'description' => 'Each monthly subscription box features a rotating mix of seasonal cookies, limited-run flavors, and a baker selection chosen for that month. It is a simple gift option for students, remote teams, and families who want fresh cookie variety without placing a new order every time.',
        ],
        10 => [
            'slug'        => 'gluten-friendly-almond-cookie',
            'name'        => 'Gluten-Friendly Almond Cookie',
            'short'       => 'Chewy almond flour cookie with dark chocolate notes.',
            'description' => 'This almond-forward cookie uses almond flour and dark chocolate for a chewy texture and a rich roasted flavor without wheat flour in the recipe. It is made in a shared kitchen, so while it is gluten-friendly for many guests, it is not certified gluten-free.',
        ],
    ];

    foreach ($catalog as &$product) {
        $product['image'] = cookie_catalog_image_url((string) $product['slug']);
    }
    unset($product);

    return $catalog;
}

function get_product_by_id(int $id): ?array
{
    $all = get_products_catalog();
    return $all[$id] ?? null;
}

function get_product_by_slug(string $slug): ?array
{
    foreach (get_products_catalog() as $product) {
        if (($product['slug'] ?? '') === $slug) {
            return $product;
        }
    }

    return null;
}
