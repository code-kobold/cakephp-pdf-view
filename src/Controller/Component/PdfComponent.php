<?php

namespace Pdf\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;

/**
 * PDF Component to respond to PDF requests.
 *
 * Employs  Pdf\View\PdfView to change output from HTML to PDF format.
 *
 * @author Ron Metten <ccct@code-kobold.de>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class PdfComponent extends Component
{

    /**
     * @var \Cake\Controller\Controller
     */
    public $Controller;

    /**
     * @var bool
     */
    public $respondAsPdf = false;

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'viewClass' => 'Pdf',
        'autoDetect' => true
    ];

    /**
     * Constructor.
     *
     * @param ComponentRegistry $collection
     * @param array             $config
     */
    public function __construct(ComponentRegistry $collection, $config = [])
    {
        $this->Controller = $collection->getController();

        $config += $this->_defaultConfig;
        parent::__construct($collection, $config);
    }

    /**
     * @inheritdoc
     */
    public function initialize(array $config = [])
    {
        if (!$this->_config['autoDetect']) {
            return;
        }
        $this->respondAsPdf = $this->Controller->request->is('pdf');
    }

    /**
     * Called before:
     *  * Controller::beforeRender()
     *  * the View class is loaded
     *  * Controller::render()
     *
     * @param Event $event
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!$this->respondAsPdf) {
            return;
        }

        $this->_respondAsPdf();
    }

    /**
     * Set view class name
     *
     * @return void
     */
    protected function _respondAsPdf()
    {
        $this->Controller->viewBuilder()->className($this->_config['viewClass']);
    }

}
