<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahallahController extends Controller
{
    public function show(Request $request, $mahallah)
    {
        //after(whitelist validation)
        $allowed = ['siddiq', 'aminah', 'ruqayyah', 'halimah', 'hafsa', 'bilal'];
        
        if (!in_array(strtolower($mahallah), $allowed)) {
            abort(404);
        }
        // Store selected mahallah in session
        session(['selected_mahallah' => $mahallah]);

        // Full menu with categories
        $menus = [
            // Drinks
            ['name' => 'Teh Tarik', 'price' => '2.00', 'image' => 'teh.jpg', 'category' => 'drinks'],
            ['name' => 'Milo Ais', 'price' => '2.50', 'image' => 'milo_ais.jpg', 'category' => 'drinks'],
            ['name' => 'Sirap Bandung', 'price' => '2.20', 'image' => 'bandung.jpg', 'category' => 'drinks'],

            // Mee
            ['name' => 'Mee Goreng', 'price' => '4.00', 'image' => 'mee_goreng.jpg', 'category' => 'mee'],
            ['name' => 'Mee Rebus', 'price' => '4.50', 'image' => 'mee_rebus.jpg', 'category' => 'mee'],
            ['name' => 'Maggi Goreng', 'price' => '3.50', 'image' => 'maggi_goreng.jpg', 'category' => 'mee'],

            // Nasi
            ['name' => 'Nasi Lemak', 'price' => '3.50', 'image' => 'nasi_lemak.jpg', 'category' => 'nasi'],
            ['name' => 'Nasi Ayam', 'price' => '5.00', 'image' => 'nasi_ayam.jpg', 'category' => 'nasi'],
            ['name' => 'Nasi Goreng', 'price' => '4.50', 'image' => 'nasi_goreng.jpg', 'category' => 'nasi'],

            // Pizza
            ['name' => 'Pizza Margherita', 'price' => '8.00', 'image' => 'pizza_margherita.jpg', 'category' => 'pizza'],
            ['name' => 'Pepperoni Pizza', 'price' => '9.00', 'image' => 'pizza_pepperoni.jpg', 'category' => 'pizza'],
            ['name' => 'Hawaiian Pizza', 'price' => '9.50', 'image' => 'pizza_hawaiian.jpg', 'category' => 'pizza'],

            // Soup
            ['name' => 'Chicken Soup', 'price' => '4.00', 'image' => 'chicken_soup.jpg', 'category' => 'soup'],
            ['name' => 'Tom Yum', 'price' => '4.50', 'image' => 'tom_yum.jpg', 'category' => 'soup'],
            ['name' => 'Mushroom Soup', 'price' => '3.80', 'image' => 'mushroom_soup.jpg', 'category' => 'soup'],
        ];

        // Handle category search
        $search = $request->query('search');
        if ($search) {
            $menus = collect($menus)->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['category']), strtolower($search));
            })->values()->all();
        }

        return view('student.food', [
            'mahallah' => ucfirst($mahallah),
            'logo' => strtolower($mahallah) . '.png', //before (path injection risk)
            'menus' => $menus,
            'otherMahallahs' => array_diff(
                ['Siddiq', 'Aminah', 'Ruqayyah', 'Halimah', 'Hafsa', 'Bilal'],
                [$mahallah]
            ),
        ]);
    }
}
