<?php

namespace App\Http\Controllers;

use App\Models\AnalyzeStrain;
use App\Models\strain;
use App\Models\Protein;
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
use GuzzleHttp\Client; 



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

        // Приводим содержимое к верхнему регистру
        $contentUpper = strtoupper($content);
        Log::info('Последовательность ДНК в верхнем регистре: '. $contentUpper);

        // Удаляем все символы, кроме A, C, T, G
        $filteredContent = preg_replace('/[^GTAC]/', '', $contentUpper);

        Log::info('Отфильтрованная последовательность ДНК: '. $filteredContent);
        // Получаем имя из поля name в запросе
        $name = $request->input('name');
        // Формируем новое имя файла с расширением .txt
        $newFileName = $name . '.txt';

        // Сохраняем отфильтрованное содержимое в новый файл
        $path = Storage::disk('public')->put('uploads/' . $newFileName, $filteredContent);


        // Получаем полный URL к файлу
        $url = Storage::url('app/public/uploads/' . $newFileName);
        Log::info($url);

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
        Log::info($request);
    
        // Валидация входных данных
        $validatedData = $request->validate([
            'name' => 'required|string', // Поле для имени штамма
            'link' => 'required|string', // Поле для содержимого файла
            'place_of_allocation' => 'required|string',
            'year_of_allocation' => 'nullable|integer|between:1970,2024',
            'type_of_bacteria' => 'required|string',
            'file_content' => 'required|string',
        ]);
    
        $strain = Strain::find($id);
        if (!$strain) {
            return $this->errorResponse('Запись не найдена', [], Response::HTTP_NOT_FOUND);
        }
    
        if (!Auth::user()->can('update', $strain)) {
            return $this->errorResponse('Отсутствуют разрешения', [], 403);
        }
    
        // Обновляем запись в базе данных
        $strain->update($validatedData);
        Log::info($validatedData);
    
        // Обновляем содержимое файла
        if (isset($validatedData['file_content'])) {
            $filePath = 'C:/ARM-genetic/backend' . $strain->link; // Используем полный путь
            Log::info($filePath);
            if ($filePath) {
                try {
                    file_put_contents($filePath, $validatedData['file_content']);
                    Log::info("Файл успешно обновлен.");
                } catch (\Exception $e) {
                    Log::error("Ошибка при обновлении файла: " . $e->getMessage());
                    return $this->errorResponse('Ошибка при обновлении файла: ' . $e->getMessage(), [], 500);
                }
            } else {
                Log::error("Путь к файлу не найден.");
                return $this->errorResponse('Путь к файлу не найден.', [], 500);
            }
        }
        
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
        $perPage = $request->get('per_page'); // Значение по умолчанию 10
        //$perPage = $request->get('per_page'); // когда пагинацию на клиенет сделаю тогда здесь уже параметр по умолчанию выставить
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
                    //$relativePath = str_replace('/storage/', '', $strain->link); // Убираем /storage/
                    $filePath = 'C:/ARM-genetic/backend' . $strain->link;
                    
                    // Получаем содержимое файла из хранилища
                    //$strain->file_content = Storage::disk('public')->get($relativePath);
                    $strain->file_content = file_get_contents($filePath);
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
        Log::info('получить !');

        // Получаем название штамма из запроса
        $strainName = $request->input('strain_name');
        if (!$strainName) {
            return response()->json(['error' => 'Штамм не указан'], 400);
        }

        // Получаем штамм из базы данных
        $strain = Strain::where('name', $strainName)->first();
        if (!$strain) {
            return response()->json(['error' => 'Штамм не найден'], 404);
        }

        // Получаем путь к файлу из ссылки
        $filePath = 'C:/ARM-genetic/backend' . $strain->link;
        Log::info($filePath);
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Файл не найден'], 404);
        }

        // Отправляем файл на сервер для анализа
        $client = new Client();
        $response = $client->post('http://localhost:5000/find_repeats', [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath),
                ],
                [
                    'name'     => 'min_length',
                    'contents' => 23,
                ],
                [
                    'name'     => 'max_length',
                    'contents' => 44,
                ]
            ]
        ]);

        $repeats = json_decode($response->getBody(), true);

        // Сохраняем результаты анализа в базе данных
        foreach ($repeats['spacers_info'] as $repeatInfo) {
            AnalyzeStrain::create([
                'author_id' => Auth::id(), // Используем Auth::id() для получения ID текущего пользователя
                'strain_id' => $strain->id,
                'repeat_sequence' => $repeatInfo['repeat'],
                'repeat_positions' => json_encode($repeatInfo['repeat_positions']), // Преобразование в JSON
                'spacer_sequence' => $repeatInfo['spacer'],
                'spacer_positions' => json_encode($repeatInfo['spacer_positions']), // Преобразование в JSON
                'is_known' => $repeatInfo['is_known'],
                'full_context' => $repeatInfo['full_context'], //
                'status' => 'на рассмотрении',
            ]);
        }

        return response()->json($repeats);
    }

    public function getAllStrainNames()
    {
        // Извлекаем все имена штаммов
        $strainNames = Strain::pluck('name');

        // Проверяем, есть ли записи
        if ($strainNames->isEmpty()) {
            return $this->errorResponse('Записи не найдены', [], Response::HTTP_NOT_FOUND);
        }

        // Возвращаем успешный ответ
        return $this->successResponse($strainNames, [], Response::HTTP_OK);
    }

    public function getAnalyzeRecordsByStrainName(Request $request)
    {
        // Получаем название штамма из запроса
        $strainName = $request->input('name');

        // Находим штамм по названию
        $strain = Strain::where('name', $strainName)->first();

        // Проверяем, найден ли штамм
        if (!$strain) {
            return response()->json([
                'message' => 'Штамм не найден'
            ], Response::HTTP_NOT_FOUND);
        }

        // Извлекаем все записи анализа для найденного штамма
        $analyzeRecords = AnalyzeStrain::where('strain_id', $strain->id)->where('status', 'на рассмотрении')->get();

        // Возвращаем записи анализа
        return response()->json($analyzeRecords, Response::HTTP_OK);
    }

    public function updateAnalyzeRecordStatus(Request $request, $id)
    {
        // Получаем новый статус из запроса
        $newStatus = $request->input('status');

        Log::info($request);

        // Находим запись по ID
        $analyzeRecord = AnalyzeStrain::find($id);

        // Проверяем, найдена ли запись
        if (!$analyzeRecord) {
            return response()->json([
                'message' => 'Запись не найдена'
            ], Response::HTTP_NOT_FOUND);
        }

        // Обновляем статус
        $analyzeRecord->status = $newStatus;
        $analyzeRecord->save();

        // Возвращаем успешный ответ
        return response()->json([
            'message' => 'Статус успешно обновлен',
            'record' => $analyzeRecord
        ], Response::HTTP_OK);
    }
}
