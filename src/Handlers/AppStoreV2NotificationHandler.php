<?php

namespace Imdhemy\Purchases\Handlers;

use Illuminate\Support\Facades\Log;
use Imdhemy\AppStore\ServerNotifications\V2DecodedPayload;

/**
 * Class AppStoreV2NotificationHandler
 * This class is used to handle AppStore V2 notifications
 */
class AppStoreV2NotificationHandler extends AbstractNotificationHandler
{
    /**
     * @var JwsServiceInterface
     */
    private JwsServiceInterface $jwsService;

    /**
     * @param HandlerHelpersInterface $helpers
     * @param JwsServiceInterface $jwsService
     */
    public function __construct(HandlerHelpersInterface $helpers, JwsServiceInterface $jwsService)
    {
        $this->jwsService = $jwsService;

        parent::__construct($helpers);
    }

    /**
     * @return void
     */
    protected function handle(): void
    {
        $decodedPayload = V2DecodedPayload::fromJws($this->jwsService->parse());

        if ($decodedPayload->getType() === V2DecodedPayload::TYPE_TEST) {
            Log::info(
                'AppStoreV2NotificationHandler: Test notification received ' .
                $this->request->get('signedPayload')
            );
        }
    }

    /**
     * @return bool
     */
    protected function isAuthorized(): bool
    {
        return parent::isAuthorized() && $this->jwsService->verify();
    }

    /**
     * @return string[][]
     */
    protected function rules(): array
    {
        return [
            'signedPayload' => ['required', 'string'],
        ];
    }
}
