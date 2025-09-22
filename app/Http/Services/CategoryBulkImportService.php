<?php
namespace App\Http\Services;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class CategoryBulkImportService
{
    public function categoryImport($validatedData)
    {
        $file = $validatedData['file'];
        $rows = array_map('str_getcsv', file($file->getRealPath()));

        $header = array_shift($rows);
        

        $categoriesInserted = 0;
        $categoriesSkipped = 0;
        $tagsInserted = 0;
        $tagsSkipped = 0;

        DB::transaction(function () use ($rows, &$categoriesInserted, &$categoriesSkipped, &$tagsInserted, &$tagsSkipped) {
            foreach ($rows as $row) {
                $rawCategoryName = trim($row[0] ?? null);
                if (!$rawCategoryName) continue;

                // Normalize category (case-insensitive)
                $categoryName = ucfirst(strtolower($rawCategoryName));

                // Check category case-insensitive
                $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
                if ($category) {
                    $categoriesSkipped++;
                } else {
                    $category = Category::create(['name' => $categoryName]);
                    $categoriesInserted++;
                }

                for ($i = 1; $i < count($row); $i++) {
                    $tagName = trim($row[$i] ?? null);
                    if (!$tagName) continue;

                    $exists = Tag::where('category_id', $category->id)
                                 ->where('name', $tagName) 
                                 ->exists();

                    if ($exists) {
                        $tagsSkipped++;
                    } else {
                        Tag::create([
                            'category_id' => $category->id,
                            'name' => $tagName
                        ]);
                        $tagsInserted++;
                    }
                }
            }
        });

        return response()->json([
            'message' => "Processed " . count($rows) . " rows",
            'categories_inserted' => $categoriesInserted,
            'categories_skipped' => $categoriesSkipped,
            'tags_inserted' => $tagsInserted,
            'tags_skipped' => $tagsSkipped,
        ]);
    }
}
