<?php
/**
 * Catalog of 10 products/services for Sweet Crumb Homemade Cookies.
 * Keys are product IDs (1–10).
 */
function get_products_catalog(): array {
    return [
        1 => [
            'name'        => 'Classic Chocolate Chip',
            'short'       => 'Buttery dough loaded with semisweet chips.',
            'description' => 'Our signature cookie: real butter, brown sugar, and premium semisweet chocolate chips. Baked until the edges are golden and the center stays soft. Perfect with milk or coffee. Available by the dozen or half-dozen.',
            'image'       => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=800&q=80',
        ],
        2 => [
            'name'        => 'Oatmeal Raisin Deluxe',
            'short'       => 'Hearty oats, plump raisins, warm spice.',
            'description' => 'Old-fashioned rolled oats, California raisins, and a hint of cinnamon and vanilla. Chewy, filling, and not too sweet—great for breakfast or an afternoon snack.',
            'image'       => 'https://images.unsplash.com/photo-1590080876410-2c2a8e7b3c5f?w=800&q=80',
        ],
        3 => [
            'name'        => 'Decorated Sugar Cookies',
            'short'       => 'Soft vanilla cookies with custom icing.',
            'description' => 'Soft cut-out sugar cookies with royal icing in your choice of colors and simple designs. Ideal for birthdays, holidays, and corporate gifts. Minimum order applies.',
            'image'       => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=800&q=80',
        ],
        4 => [
            'name'        => 'Snickerdoodle',
            'short'       => 'Cinnamon-sugar crackle, pillowy center.',
            'description' => 'Cream of tartar gives these their classic tang; the outside is rolled in cinnamon sugar for a crisp, spicy shell and a tender middle.',
            'image'       => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800&q=80',
        ],
        5 => [
            'name'        => 'Double Chocolate Fudge',
            'short'       => 'Cocoa dough plus dark chocolate chunks.',
            'description' => 'For chocolate lovers: rich cocoa batter folded with dark chocolate chunks. Dense, fudgy, and intense. Pairs well with espresso.',
            'image'       => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800&q=80',
        ],
        6 => [
            'name'        => 'Lemon Glazed Shortbread',
            'short'       => 'Buttery shortbread with bright lemon glaze.',
            'description' => 'Crisp shortbread base with fresh lemon zest in the dough and a thin tangy glaze on top. Light and refreshing for spring and summer events.',
            'image'       => 'https://images.unsplash.com/photo-1621303837174-89787a7d4729?w=800&q=80',
        ],
        7 => [
            'name'        => 'Peanut Butter Blossom',
            'short'       => 'Soft peanut butter with a chocolate kiss.',
            'description' => 'Classic peanut butter cookies rolled in sugar, baked, and topped with a milk chocolate center. Nostalgic and crowd-pleasing.',
            'image'       => 'https://images.unsplash.com/photo-1599735219378-2b5c8a3b6c5f?w=800&q=80',
        ],
        8 => [
            'name'        => 'Custom Catering Tray',
            'short'       => 'Assorted cookies for meetings and parties.',
            'description' => 'Large platters with a mix of our bestsellers (minimum 48 pieces). We label allergens on request and deliver within our service area. Perfect for office meetings and celebrations.',
            'image'       => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=800&q=80',
        ],
        9 => [
            'name'        => 'Gift Box Subscription',
            'short'       => 'Monthly curated cookie box shipped or picked up.',
            'description' => 'Subscribe for a monthly box of seasonal flavors plus a rotating “baker’s choice.” Skip or cancel anytime. Great gift for students and remote teams.',
            'image'       => 'https://images.unsplash.com/photo-1548365328-9c3a75d7f2f0?w=800&q=80',
        ],
        10 => [
            'name'        => 'Gluten-Friendly Almond Cookie',
            'short'       => 'Almond flour base; made in a home kitchen.',
            'description' => 'Chewy cookies made with almond flour and dark chocolate. Not certified gluten-free (shared kitchen) but no wheat flour in the recipe. Ask for ingredient list.',
            'image'       => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=800&q=80',
        ],
    ];
}

function get_product_by_id(int $id): ?array {
    $all = get_products_catalog();
    return $all[$id] ?? null;
}
