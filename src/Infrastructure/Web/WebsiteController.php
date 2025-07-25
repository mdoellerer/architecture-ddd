<?php

namespace Infrastructure\Web;

use Application\Website\WebsiteService;

class WebsiteController
{
    public function __construct(private WebsiteService $service) {}

    private function validateRequest(array $request): void
    {
        if (empty($request["parent"]) || empty($request['address']) || empty($request['source'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid or Missing arguments']);
            exit;
        }
    }

    public function create(array $request): void
    {
        $this->validateRequest($request);
        $website = $this->service->create($request['parent'], $request['address'], $request['source']);
        
        echo json_encode(['id' => $website->getId()]);
    }

    public function update(array $request, string $id): void
    {
        $this->validateRequest($request);
        $website = $this->service->update($request['parent'], $request['address'], $request['source'], $id);
        
        if ($website) {
            echo json_encode([
                'id' => $website->getId(),
                'company_id' => $website->getCompanyId(),
                'address' => $website->getAddress(),
                'source' => $website->getSource()->getName()
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Website not found']);
        }
    }

    public function list(?array $request): void
    {
        $websites = $this->service->all($request['parent'] ?? null);
        
        echo json_encode(array_map(fn($w) => [
            'id' => $w->getId(),
            'company_id' => $w->getCompanyId(),
            'address' => $w->getAddress(),
            'source' => $w->getSource()->getName()
        ], $websites));
    }

    public function get(string $id): void
    {
        $website = $this->service->get($id);
        if ($website) {
            echo json_encode([
                'id' => $website->getId(),
                'company_id' => $website->getCompanyId(),
                'address' => $website->getAddress(),
                'source' => $website->getSource()->getName()
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Website not found']);
        }
    }

    public function delete(string $id): void
    {
        $this->service->delete($id);
        
        echo json_encode(['deleted' => true]);
    }
}
