<?php

namespace App\Http\Controllers;

use App\Models\strain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreStrainRequest;
use App\Http\Requests\UpdateStrainRequest;
use App\Traits\PaginationTrait;
use App\Traits\QueryBuilderTrait;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;


class StrainController extends Controller
{
    use QueryBuilderTrait, PaginationTrait;
    public function store(StoreStrainRequest $request)
    {
        if (!Auth::user()->can('create', Strain::class)) {
            return $this->errorResponse('Отсутствуют разрешения', [], 403);
        }

        // Проверяем, был ли загружен файл
        if ($request->hasFile('file')) {
            // Получаем файл
            $file = $request->file('file');

            // Проверяем, что файл имеет правильный формат
            if ($file->getClientOriginalExtension() !== 'txt') {
                return $this->errorResponse('Файл должен быть формата .txt', [], 400);
            }

            // Читаем содержимое файла
            $content = file_get_contents($file->getRealPath());

            // Удаляем все символы, кроме A, C, T, G
            $filteredContent = preg_replace('/[^ACTG]/', '', $content);

            // Получаем имя из поля name в запросе
            $name = $request->input('name');
            // Формируем новое имя файла с расширением .txt
            $newFileName = $name . '.txt';

           // Сохраняем отфильтрованное содержимое в новый файл
            $path = Storage::disk('public')->put('uploads/' . $newFileName, $filteredContent);

            // Получаем полный URL к файлу
            $url = Storage::url('uploads/' . $newFileName);

            // Создаем запись в базе данных
            $strain = Strain::create($request->validated() + [
                'author_id' => Auth::id(),
                'link' => $url, // Сохраняем полный URL к файлу в поле link
            ]);

            return $this->successResponse($strain, 'Запись о штамме успешно создана', 200);
        }

        return $this->errorResponse('Файл не загружен', [], 400);
    }


    public function update(UpdateStrainRequest $request, int $id)
    {
        Log::info('Update');
        
        $strain = Strain::find($id);
        if (!$strain) {
            return $this->errorResponse('Запись не найдена', [], Response::HTTP_NOT_FOUND);
        }

        if (!Auth::user()->can('update', $strain)) {
            return $this->errorResponse('Отсутствуют разрешения', [], 403);
        }

        $validatedData = $request->validated();

        $strain->update($validatedData);
        
        return $this->successResponse(['strain' => $strain], 'Запись о штамме успешно обновлена', 200);
    }

    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        $strain = Strain::find($id);

        if (!$strain) {
            return $this->errorResponse('Запись не найдена', [], Response::HTTP_NOT_FOUND);
        }

        if (!Auth::user()->can('delete', $strain)) {
            return $this->errorResponse('Отсутствуют разрешения', [], 403);
        }

        // Удаляем файл из хранилища, если он существует
        if ($strain->link) {
            Storage::disk('public')->delete($strain->link);
        }

        // Удаляем запись из базы данных
        $strain->delete();

        return $this->successResponse(['strain' => $strain], 'Запись о штамме успешно удалена', 200); 
    }

    public function getStrains(Request $request)
    {
        // Проверка прав доступа
        // if (!Auth::user()->can('viewAny', Strain::class)) {
        //     return $this->errorResponse('Нет прав на просмотр', [], Response::HTTP_FORBIDDEN);
        // }

        // Начинаем запрос
        $query = Strain::query();

        // Применяем фильтры, если они заданы
        if ($request->has('type_of_bacteria')) {
            $query->where('type_of_bacteria', $request->input('type_of_bacteria'));
        }
        if ($request->has('id')) {
            $query->where('id', $request->input('id'));
        }
        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
        }
        if ($request->has('link')) {
            $query->where('link', 'LIKE', '%' . $request->input('link') . '%');
        }
        if ($request->has('place_of_allocation')) {
            $query->where('place_of_allocation', 'LIKE', '%' . $request->input('place_of_allocation') . '%');
        }
        if ($request->has('year_of_allocation')) {
            $query->where('year_of_allocation', $request->input('year_of_allocation'));
        }

        // Получаем результаты с пагинацией
        $perPage = $request->get('per_page', 10); // Значение по умолчанию 10
        $strains = $query->paginate($perPage);

        // Проверяем, есть ли записи
        if ($strains->isEmpty()) {
            return $this->errorResponse('Записи не найдены', [], Response::HTTP_NOT_FOUND);
        }

       // Если указан ID, добавляем содержимое файла
        if ($request->has('id')) {
            foreach ($strains as $strain) {
                if ($strain->link) {
                    // Извлекаем относительный путь из URL
                    $relativePath = str_replace('/storage/', '', $strain->link); // Убираем /storage/
                    
                    // Получаем содержимое файла из хранилища
                    $strain->file_content = Storage::disk('public')->get($relativePath);
                }
            }
        }

        // Формируем данные пагинации
        $paginationData = $this->makePaginationData($strains);

        // Возвращаем успешный ответ
        return $this->successResponse($strains->items(), $paginationData, Response::HTTP_OK);
    }


    public function findRepeats(Request $request)
    {
        $sequence = $request->input('sequence');
        $minLength = $request->input('min_length', 23); // Минимальная длина повторяющейся последовательности

        $repeats = $this->findRepeatingSequences($sequence, $minLength);

        return response()->json($repeats);
    }

    private function findRepeatingSequences($sequence, $minLength)
    {
        $length = strlen($sequence);
        $repeats = [];

        for ($i = 0; $i < $length; $i++) {
            for ($j = $i + $minLength; $j <= $length; $j++) {
                $subSeq = substr($sequence, $i, $j - $i);
                if (substr_count($sequence, $subSeq) > 1) {
                    $repeats[$subSeq] = substr_count($sequence, $subSeq);
                }
            }
        }

        return $repeats;
    }
        

}
