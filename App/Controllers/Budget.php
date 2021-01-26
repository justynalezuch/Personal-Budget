<?php


namespace App\Controllers;
use App\Models\IncomeCategoryAssignedToUser;
use Core\View;

class Budget extends Authenticated
{
    public function addIncomeAction() {

        $incomeCategories = IncomeCategoryAssignedToUser::getAll();

        foreach ($incomeCategories as $key => $category) {
            $incomeCategories[$key]['slug'] = static::createSlugFromString($category['name']);
        }

        View::renderTemplate('Income/new.html', [
            'income_categories' => $incomeCategories
        ]);
    }

    /**
     * Create lowercase slug
     *
     * @param $string
     * @return string
     */
    public static function createSlugFromString($string) {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        return strtolower($slug);
    }

}