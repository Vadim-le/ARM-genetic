<?php

namespace App\Http\Controllers;

use App\Models\strain;
use App\Models\Protein;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\PaginationTrait;
use App\Traits\QueryBuilderTrait;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;


class ProteinController extends Controller
{
    //ПЕРЕВОД ДНК В БЕЛОК!!!    
    public function translate(Request $request)
    {
        //$request->validate([
        //    'strain_name' => 'required|string', // Поле для имени штамма
        //]);

        // Получаем штамм по имени
        $strain = Strain::where('name', $request->input('name'))->first();
        if (!$strain) {
            return response()->json(['error' => 'Штамм не найден'], 404);
        }

        // Получаем последовательность ДНК из файла
        // Извлекаем относительный путь из ссылки
        $relativePath = str_replace('/storage/', '', $strain->link);

        // Получаем содержимое файла из хранилища
        $dnaSequence = Storage::disk('public')->get($relativePath);
        Log::info('Полученная последовательность ДНК: ' . $dnaSequence);
        $dnaSequence = strtoupper(trim($dnaSequence)); // Приводим к верхнему регистру и убираем пробелы
        Log::info('Последовательность ДНК в верхнем регистре: ' . $dnaSequence);

        // Переводим ДНК в белок
        $proteinSequence = $this->dnaToProtein($dnaSequence);
        Log::info('Последовательность ДНК в верхнем регистре: ' . $proteinSequence);

         // Сохраняем последовательность белка в файл
        $proteinFileName = $strain->name . '_protein.txt';
        $proteinFilePath = Storage::disk('public')->put('uploads/proteins/' . $proteinFileName, $proteinSequence);

        // Получаем полный URL к файлу
        $proteinUrl = Storage::url('uploads/' . $proteinFileName);
        $proteiname = ($strain->name);
        // Сохраняем информацию о белке в таблице proteins
        $protein = Protein::create([
            'name' => $proteiname,
            'link' => $proteinUrl,
            'author_id' => auth()->id(), // Предполагается, что пользователь аутентифицирован
            'strain_id' => $strain->id,
        ]);

        return response()->json([
            'protein_sequence' => $proteinSequence,
        ]);
    }

    private function dnaToProtein($dna)
    {
        // Кодоны для аминокислот
        $codonTable = [
            'ATA' => 'I', 'ATC' => 'I', 'ATT' => 'I', 'ATG' => 'M',
            'ACA' => 'T', 'ACC' => 'T', 'ACG' => 'T', 'ACT' => 'T',
            'AAC' => 'N', 'AAT' => 'N', 'AAA' => 'K', 'AAG' => 'K',
            'AGC' => 'S', 'AGT' => 'S', 'AGA' => 'R', 'AGG' => 'R',
            'CTA' => 'L', 'CTC' => 'L', 'CTG' => 'L', 'CTT' => 'L',
            'CCA' => 'P', 'CCC' => 'P', 'CCG' => 'P', 'CCT' => 'P',
            'CAC' => 'H', 'CAT' => 'H', 'CAA' => 'Q', 'CAG' => 'Q',
            'CGA' => 'R', 'CGC' => 'R', 'CGG' => 'R', 'CGT' => 'R',
            'GTA' => 'V', 'GTC' => 'V', 'GTG' => 'V', 'GTT' => 'V',
            'GCA' => 'A', 'GCC' => 'A', 'GCG' => 'A', 'GCT' => 'A',
            'GAC' => 'D', 'GAT' => 'D', 'GAA' => 'E', 'GAG' => 'E',
            'GGA' => 'G', 'GGC' => 'G', 'GGG' => 'G', 'GGT' => 'G',
            'TCA' => 'S', 'TCC' => 'S', 'TCG' => 'S', 'TCT' => 'S',
            'TTC' => 'F', 'TTT' => 'F', 'TTA' => 'L', 'TTG' => 'L',
            'TAC' => 'Y', 'TAT' => 'Y', 'TAA' => '_', 'TAG' => '_',
            'TGC' => 'C', 'TGT' => 'C', 'TGA' => '_', 'TGG' => 'W',
        ];

        $protein = '';
        // Разбиваем ДНК на кодоны (по 3 нуклеотида)
        for ($i = 0; $i < strlen($dna); $i += 3) {
            $codon = substr($dna, $i, 3);
            if (array_key_exists($codon, $codonTable)) {
                $protein .= $codonTable[$codon];
            } else {
                $protein .= '?'; // Неизвестный кодон
            }
        }

        return $protein;
    }

    //АНАЛИЗ ГИДРОФОБНОСТИ БЕЛКА!!!
}
