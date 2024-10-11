<?php

namespace App\Http\Controllers;

use App\Models\Strain;
use App\Models\Protein;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProteinController extends Controller
{
    public function analyze(Request $request)
    {
        $mode = $request->input('mode');
        $analyses = json_decode($request->input('analyses', '[]'), true); // Преобразуем JSON-строку в массив
        $scales = json_decode($request->input('scales', '[]'), true); // Преобразуем JSON-строку в массив
        $isotopes = $request->input('isotopes', []);
        $pH = $request->input('pH');

        $proteinSequence = '';

    
        // Проверяем, был ли передан файл
        if ($request->has('database_file_name')) {
            // Получаем данные из базы данных
            $fileName = $request->input('database_file_name');
            Log::info($fileName);

            $fileRecord = Strain::where('name', $fileName)->first();

            if (!$fileRecord) {
                return response()->json(['error' => 'Файл не найден в базе данных'], 404);
            }
    
            $filePath = 'C:/ARM-genetic/backend' . $fileRecord->link;

            $sequence= file_get_contents($filePath);
        } elseif ($request->hasFile('file')) {
            // Получаем файл из запроса
            $file = $request->file('file');
            $sequence = strtoupper(trim(file_get_contents($file->getRealPath())));
        } else {
            return response()->json(['error' => 'Файл не передан'], 400);
        }
        
        if ($mode === 'DNA') {
            // Переводим ДНК в белок
            $proteinSequence = $this->dnaToProtein($sequence);
        } elseif ($mode === 'Protein') {
            // Используем последовательность белка напрямую
            $proteinSequence = $sequence;
        } else {
            return response()->json(['error' => 'Неверный режим анализа'], 400);
        }
    
        $results = [];
    
        // Выполняем анализы в зависимости от запроса
        if (in_array('hydrophobicity', $analyses)) {
            $results['hydrophobicity'] = $this->analyzeHydrophobicity($proteinSequence, $scales);
        }
    
        if (in_array('molecular_mass', $analyses)) {
            $results['molecular_mass'] = $this->calculateMolecularMass($proteinSequence, $isotopes);
        }
    
        if (in_array('amino_acid_content', $analyses)) {
            $results['amino_acid_content'] = $this->calculateAminoAcidContent($proteinSequence);
        }
    
        if (in_array('isoelectric_point', $analyses)) {
            $results['isoelectric_point'] = $this->calculateIsoelectricPoint($proteinSequence);
        }
    
        if (in_array('protein_charge', $analyses)) {
            $results['protein_charge'] = $this->calculateProteinCharge($proteinSequence, $pH);
        }
    
        return response()->json($results);
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

    private function analyzeHydrophobicity($proteinSequence, $scales)
    {
        $hydrophobicity = [];

        // Шкала Кайте-Дулиттла
        $kyteDoolittleScale = [
            'A' => -0.5, 'R' => -4.5, 'N' => -3.5, 'D' => -3.5,
            'C' => 2.5, 'E' => -3.5, 'Q' => -3.5, 'G' => -0.4,
            'H' => -3.2, 'I' => 4.5, 'L' => 3.8, 'K' => -3.9,
            'M' => 1.9, 'F' => 2.8, 'P' => -1.6, 'S' => -0.8,
            'T' => -0.7, 'W' => -0.9, 'Y' => -1.3, 'V' => 4.2,
        ];

        // Шкала Хопфилда
        $hopfieldScale = [
            'A' => 1.8, 'R' => -2.5, 'N' => -0.6, 'D' => -0.9,
            'C' => 2.5, 'E' => -0.7, 'Q' => -0.7, 'G' => -0.4,
            'H' => -0.4, 'I' => 4.5, 'L' => 3.8, 'K' => -1.5,
            'M' => 1.9, 'F' => 2.8, 'P' => -1.6, 'S' => -0.8,
            'T' => -0.7, 'W' => -0.9, 'Y' => -1.3, 'V' => 4.2,
        ];

        // Шкала Гриффита
        $griffithScale = [
            'A' => 1.8, 'R' => -4.5, 'N' => -3.5, 'D' => -3.5,
            'C' => 2.5, 'E' => -3.5, 'Q' => -3.5, 'G' => -0.4,
            'H' => -3.2, 'I' => 4.5, 'L' => 3.8, 'K' => -3.9,
            'M' => 1.9, 'F' => 2.8, 'P' => -1.6, 'S' => -0.8,
            'T' => -0.7, 'W' => -0.9, 'Y' => -1.3, 'V' => 4.2,
        ];
        

        foreach ($scales as $scale) {
            switch ($scale) {
                case 'kyteDoolittle':
                    $hydrophobicity[$scale] = $this->calculateHydrophobicity($proteinSequence, $kyteDoolittleScale);
                    break;
                case 'hopfield':
                    $hydrophobicity[$scale] = $this->calculateHydrophobicity($proteinSequence, $hopfieldScale);
                    break;
                case 'griffith':
                    $hydrophobicity[$scale] = $this->calculateHydrophobicity($proteinSequence, $griffithScale);
                    break;
                default:
                    $hydrophobicity[$scale] = 'Шкала не найдена';
            }
        }

        return $hydrophobicity;
    }

    private function calculateHydrophobicity($proteinSequence, $scale)
    {
        $hydrophobicity = 0;
        foreach (str_split($proteinSequence) as $aminoacid) {
            $hydrophobicity += $scale[$aminoacid] ?? 0;
        }
        return $hydrophobicity / strlen($proteinSequence);
    }

    private function calculateMolecularMass($proteinSequence, $isotopes = [])
    {
        // Молекулярные массы аминокислот в их естественном содержании
        $aminoacidMasses = [
            'A' => 71.03711, 'R' => 156.10111, 'N' => 114.04293, 'D' => 115.02694,
            'C' => 103.00919, 'E' => 129.04259, 'Q' => 128.05858, 'G' => 57.02146,
            'H' => 137.05891, 'I' => 113.08406, 'L' => 113.08406, 'K' => 128.09496,
            'M' => 131.04049, 'F' => 147.06841, 'P' => 97.05276, 'S' => 87.03203,
            'T' => 101.04768, 'W' => 186.07931, 'Y' => 163.06333, 'V' => 99.06841,
        ];
    
        // Изменение массы для изотопов
        $isotopeMasses = [
            '13C' => 1.00335, // Разница между 12C и 13C
            '15N' => 0.99703, // Разница между 14N и 15N
            'D' => 1.00628,   // Разница между H и D (дейтерий)
        ];
    
        // Вычисляем брутто-формулу белка
        $formula = [];
        foreach (str_split($proteinSequence) as $aminoacid) {
            if (isset($aminoacidMasses[$aminoacid])) {
                $formula[$aminoacid] = ($formula[$aminoacid] ?? 0) + 1;
            }
        }
    
        // Рассчитываем массу
        $molecularMass = 0;
        foreach ($formula as $aminoacid => $count) {
            $mass = $aminoacidMasses[$aminoacid] * $count;
    
            // Добавляем изотопную массу, если изотоп указан
            foreach ($isotopes as $isotope) {
                if (isset($isotopeMasses[$isotope])) {
                    $mass += $isotopeMasses[$isotope] * $count;
                }
            }
    
            $molecularMass += $mass;
        }
    
        return $molecularMass;
    }

    private function calculateAminoAcidContent($proteinSequence)
{
    // Инициализируем массивы для подсчёта аминокислот
    $aminoAcidCount = [
        'A' => 0, 'R' => 0, 'N' => 0, 'D' => 0,
        'C' => 0, 'E' => 0, 'Q' => 0, 'G' => 0,
        'H' => 0, 'I' => 0, 'L' => 0, 'K' => 0,
        'M' => 0, 'F' => 0, 'P' => 0, 'S' => 0,
        'T' => 0, 'W' => 0, 'Y' => 0, 'V' => 0,
    ];

    // Подсчитываем количество каждой аминокислоты
    foreach (str_split($proteinSequence) as $aminoacid) {
        if (isset($aminoAcidCount[$aminoacid])) {
            $aminoAcidCount[$aminoacid]++;
        }
    }

    // Рассчитываем процентное содержание
    $totalAminoAcids = strlen($proteinSequence);
    $aminoAcidPercentage = [];
    foreach ($aminoAcidCount as $aminoacid => $count) {
        $aminoAcidPercentage[$aminoacid] = ($count / $totalAminoAcids) * 100;
    }

    return [
        'count' => $aminoAcidCount,
        'percentage' => $aminoAcidPercentage,
    ];
}
private function calculateIsoelectricPoint($proteinSequence)
{
    // pKa значения для терминальных групп и боковых цепей аминокислот
    $pKaValues = [
        'N_term' => 9.69, 'C_term' => 2.34,
        'D' => 3.86, 'E' => 4.25, 'C' => 8.33, 'Y' => 10.07,
        'K' => 10.54, 'R' => 12.48, 'H' => 6.00,
    ];

    // Начальные значения pH для дихотомии
    $lowPH = 0.0;
    $highPH = 14.0;
    $midPH = 0.0;
    $tolerance = 0.001; // Точность

    // Функция для расчёта заряда белка при заданном pH
    $calculateCharge = function($pH) use ($proteinSequence, $pKaValues) {
        $charge = 0.0;

        // Заряд N-концевой группы
        $charge += 1 / (1 + pow(10, $pH - $pKaValues['N_term']));

        // Заряд C-концевой группы
        $charge -= 1 / (1 + pow(10, $pKaValues['C_term'] - $pH));

        // Заряд боковых цепей аминокислот
        foreach (count_chars($proteinSequence, 1) as $aminoacid => $count) {
            $aminoacid = chr($aminoacid);
            if (isset($pKaValues[$aminoacid])) {
                if (in_array($aminoacid, ['D', 'E', 'C', 'Y'])) {
                    // Кислые аминокислоты
                    $charge -= $count / (1 + pow(10, $pKaValues[$aminoacid] - $pH));
                } elseif (in_array($aminoacid, ['K', 'R', 'H'])) {
                    // Основные аминокислоты
                    $charge += $count / (1 + pow(10, $pH - $pKaValues[$aminoacid]));
                }
            }
        }

        return $charge;
    };

    // Метод дихотомии для нахождения pH, при котором заряд равен 0
    while (($highPH - $lowPH) > $tolerance) {
        $midPH = ($lowPH + $highPH) / 2;
        $charge = $calculateCharge($midPH);

        if ($charge > 0) {
            $lowPH = $midPH;
        } else {
            $highPH = $midPH;
        }
    }

    return $midPH;
}
private function calculateProteinCharge($proteinSequence, $pH)
{
    // pKa значения для терминальных групп и боковых цепей аминокислот
    $pKaValues = [
        'N_term' => 9.69, 'C_term' => 2.34,
        'D' => 3.86, 'E' => 4.25, 'C' => 8.33, 'Y' => 10.07,
        'K' => 10.54, 'R' => 12.48, 'H' => 6.00,
    ];

    // Инициализируем заряд
    $charge = 0.0;

    // Заряд N-концевой группы
    $charge += 1 / (1 + pow(10, $pH - $pKaValues['N_term']));

    // Заряд C-концевой группы
    $charge -= 1 / (1 + pow(10, $pKaValues['C_term'] - $pH));

    // Подсчитываем количество каждой аминокислоты
    $aminoAcidCounts = array_count_values(str_split($proteinSequence));

    // Заряд боковых цепей аминокислот
    foreach ($aminoAcidCounts as $aminoacid => $count) {
        if (isset($pKaValues[$aminoacid])) {
            if (in_array($aminoacid, ['D', 'E', 'C', 'Y'])) {
                // Кислые аминокислоты
                $charge -= $count / (1 + pow(10, $pKaValues[$aminoacid] - $pH));
            } elseif (in_array($aminoacid, ['K', 'R', 'H'])) {
                // Основные аминокислоты
                $charge += $count / (1 + pow(10, $pH - $pKaValues[$aminoacid]));
            }
        }
    }

    return $charge;
}
}