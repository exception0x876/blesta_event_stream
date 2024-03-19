<?php
/**
 * Event Stream manage plugin controller
 *
 * @package blesta
 * @subpackage blesta.plugins.event_stream
 */
class AdminManagePlugin extends AppController
{
    /**
     * Performs necessary initialization
     */
    private function init()
    {
        // Require login
        $this->parent->requireLogin();

        Language::loadLang('event_stream_manage_plugin', null, PLUGINDIR . 'event_stream' . DS . 'language' . DS);

        // Set the company ID
        $this->company_id = Configure::get('Blesta.company_id');

        // Set the plugin ID
        $this->plugin_id = (isset($this->get[0]) ? $this->get[0] : null);

        // Set the page title
        $this->parent->structure->set(
            'page_title',
            Language::_(
                'EventStreamManagePlugin.'
                . Loader::fromCamelCase($this->action ? $this->action : 'index') . '.page_title',
                true
            )
        );

        // Set the view to render for all actions under this controller
        $this->view->setView(null, 'EventStream.default');
    }

    /**
     * Returns the view to be rendered when managing this plugin
     */
    public function index()
    {
        $this->init();

        if (!empty($this->post)) {
            $this->parent->Companies->setSetting($this->company_id, 'event_stream.endpoint', $this->post['endpoint']);
            $this->parent->Companies->setSetting($this->company_id, 'event_stream.private_key', $this->post['private_key']);
            $this->parent->Companies->setSetting($this->company_id, 'event_stream.allow_origin', $this->post['allow_origin']);
        }

        $endpoint = $this->parent->Companies->getSetting($this->company_id, 'event_stream.endpoint');
        $private_key = $this->parent->Companies->getSetting($this->company_id, 'event_stream.private_key');
        $allow_origin = $this->parent->Companies->getSetting($this->company_id, 'event_stream.allow_origin');
        $vars = (object)[
            'endpoint' => $endpoint->value,
            'private_key' => $private_key->value,
            'allow_origin' => $allow_origin->value,
        ];

        // Set the view to render
        return $this->partial('admin_manage_plugin', compact('vars'));
    }
}
