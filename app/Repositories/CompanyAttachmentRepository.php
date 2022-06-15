<?php

namespace App\Repositories;

use App\Entities\CompanyAttachment;
use App\Http\Criterias\SearchCriteria;
use App\Http\Presenters\DataPresenter;
use App\Http\Resources\CompanyAttachmentResource;
use App\Services\GoogleCloud\Spaces as GoogleSpaces;
use Illuminate\Http\Request;
use Log;

class CompanyAttachmentRepository extends BaseRepository
{
    public function __construct(GoogleSpaces $GoogleSpaces)
    {
        $this->GoogleSpaces = $GoogleSpaces;
        parent::__construct(CompanyAttachment::class);
    }

    public function browse(Request $request)
    {
        $this->query = $this->getModel();
        $this->applyCriteria(new SearchCriteria($request));
        $presenter = new DataPresenter(CompanyAttachmentResource::class, $request);

        return $presenter
            ->preparePager()
            ->renderCollection($this->query);
    }

    public function browseByCompany($id, Request $request)
    {
        $this->query = $this->getModel()->where('company_id', $id);
        $this->applyCriteria(new SearchCriteria($request));
        $presenter = new DataPresenter(CompanyAttachmentResource::class, $request);

        return $presenter
            ->preparePager()
            ->renderCollection($this->query);
    }

    public function show($id, Request $request)
    {
        $this->query = $this->getModel()->where(['id' => $id, 'company_id' => $this->userLogin()->company_id]);
        $presenter = new DataPresenter(CompanyAttachmentResource::class, $request);

        return $presenter->render($this->query);
    }

    public function store($request)
    {
        try {
            $payload = $request->all();
            $payload["company_id"] = $this->userLogin()->company_id;

            $file = $request->file('file');
            $exploded = explode(".", $file->getClientOriginalName());
            $originalFilename = $exploded[0];

            $payload['file_name'] = $this->generateFilename($file, $payload['attachment_type'], $payload['company_id']);

            // Upload to Google Spaces
            $results = $this->GoogleSpaces->upload('company_attachments', $payload['file_name'], $file);
            $payload['file_location'] = $results['folder_url'];
            // $payload["attachment_type"] = $payload['attachment_type'];
            // unset($payload["type"]);

            $companyAttachment = CompanyAttachment::create($payload);

            return $this->show($companyAttachment->id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function storeCompanyAttachment($payload)
    {
        try {

            $file = $payload['file'];
            $exploded = explode(".", $file->getClientOriginalName());
            $originalFilename = $exploded[0];
            $payload['file_name'] = $this->generateFilename($file, $payload['attachment_type'], $payload['company_id']);

            // Upload to Google Spaces
            $results = $this->GoogleSpaces->upload('company_attachments', $payload['file_name'], $file);
            $payload['file_location'] = $results['folder_url'];
            

            CompanyAttachment::updateOrCreate([
                'attachment_type' => $payload['attachment_type'],
                'company_id' => $payload['company_id'],
            ], $payload);

            return true;

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

    }

    public function update($id, $request)
    {
        try {
            $payload = $request->all();
            $payload['company_id'] = $this->userLogin()->company_id;

            if ($request->has('file') && $request->has('attachment_type')) {
                $file = $request->file('file');

                $payload['file_name'] = $this->generateFilename($file, $payload['attachment_type'], $payload['company_id']);

                // Upload to Google Spaces
                $results = $this->GoogleSpaces->upload('company_attachments', $payload['file_name'], $file);
                $payload['file_location'] = $results['folder_url'];
            }
            CompanyAttachment::findOrFail($id)->update($payload);
            return $this->show($id, $request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            CompanyAttachment::findOrFail($id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'data has been deleted',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    protected function generateFilename($file, $type, $companyId)
    {
        $result = 'companyId' . $companyId . "_" . preg_replace('/\s+/', '', $type) . '_userId' . $this->userLogin()->id . '.' . $file->getClientOriginalExtension();
        return $result;
    }
}
