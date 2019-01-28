<?php

namespace RuneBlogPagination\Subscriber;

use Enlight_Components_Session_Namespace;
use Shopware\Components\Plugin\CachedConfigReader;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class BlogSubscriber
 * @package RuneBlogPagination\Subscriber
 */
class BlogSubscriber implements \Enlight\Event\SubscriberInterface
{
    /**
     * @var Enlight_Components_Session_Namespace
     */
    private $session;

    /**
     * @var CachedConfigReader
     */
    private $config;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var int
     */
    private $original;

    /**
     * BlogSubscriber constructor.
     * @param Enlight_Components_Session_Namespace $session
     * @param CachedConfigReader $config
     * @param Container $container
     */
    public function __construct(
        Enlight_Components_Session_Namespace $session,
        CachedConfigReader $config,
        Container $container
    ) {
        $this->session = $session;
        $this->config = $config;
        $this->container = $container;
        $this->original = (int) $this->session->sPerPage;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PreDispatch_Frontend_Blog' => 'beforeIndexAction',
            'Enlight_Controller_Action_PostDispatch_Frontend_Blog' => 'afterIndexAction',
        ];
    }

    /**
     *
     */
    public function beforeIndexAction()
    {
        $config = $this->config->getByPluginName('RuneBlogPagination', $this->getShop());

        $this->session->sPerPage = $config['rBlogArticlesPerPage'];
    }

    /**
     *
     */
    public function afterIndexAction()
    {
        $this->session->sPerPage = $this->original;
    }

    /**
     * @return \Shopware\Models\Shop\Shop
     */
    private function getShop()
    {
        $shop = null;
        if ($this->container->initialized('shop')) {
            $shop = $this->container->get('shop');
        }

        if (!$shop) {
            $shop = $this->container->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault();
        }

        return $shop;
    }
}
