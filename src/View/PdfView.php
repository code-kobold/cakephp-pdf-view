<?php

namespace Pdf\View;

use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\View\View;
use PHPPdf\Core\FacadeBuilder;
use PHPPdf\DataSource\DataSource;

/**
 * View to handle PDF requests
 * using psliwa/php-pdf
 *
 * Covers incoming requests with '.pdf' extension.
 *
 * @author Ron Metten <ccct@code-kobold.de>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class PdfView extends View
{

    /**
     * Controller variables to provide as View class properties
     *
     * Possible variables:
     * 'autoLayout'
     * 'ext'
     * 'helpers'
     * 'layout'
     * 'layoutPath'
     * 'name'
     * 'passedArgs'
     * 'plugin'
     * 'subDir'
     * 'template'
     * 'templatePath'
     * 'theme'
     * 'view'
     * 'viewVars'
     *
     * @var array
     */
    protected $_passedVars = [
        'autoLayout',
        'ext',
        'helpers',
        'layout',
        'layoutPath',
        'name',
        'passedArgs',
        'plugin',
        'subDir',
        'template',
        'templatePath',
        'theme',
        'view',
        'viewVars',
    ];

    /**
     * View templates subdirectory.
     * /pdf
     *
     * @var string
     */
    public $subDir = null;

    /**
     * Layout name for this View.
     *
     * @var string
     */
    public $layout = '';

    /**
     * Constructor
     *
     * @param Request|null $request Request instance.
     * @param Response|null $response Response instance.
     * @param EventManager|null $eventManager Event manager instance.
     * @param array $viewOptions View options. cf. $_passedVars
     */
    public function __construct(
        Request $request = null,
        Response $response = null,
        EventManager $eventManager = null,
        array $viewOptions = []
    )
    {
        parent::__construct($request, $response, $eventManager, $viewOptions);

        if ($this->subDir === null) {
            $this->subDir = 'pdf';
            $this->templatePath = str_replace(DS . 'pdf', '', $this->templatePath);
        }

        if (isset($response)) {
            $response->type('pdf');
        }

        /**
         * Use a custom extension here, to prevent IDE like PHPStorm
         * from complaining about inspections
         */
        $this->_ext = '.xctp';
    }

    /**
     * Renders a PDF view.
     *
     * Employs Cake\View\View::render() to parse templates,
     * builds the PDF from that result and returns this PDF
     * with the response object.
     *
     * @param string $view Rendering view.
     * @param string $layout Rendering layout.
     *
     * @return string Rendered view.
     */
    public function render($view = null, $layout = null)
    {
        $pathinfo = pathinfo($this->_getViewFileName());
        $stylesheetName = $pathinfo['dirname'] . DS . $pathinfo['filename'] . '.style.xml';

        $content = parent::render($view, $layout);
        $facade = FacadeBuilder::create()->build();

        $stylesheetXml = file_get_contents($stylesheetName);
        $stylesheet = DataSource::fromString($stylesheetXml);
        $content = $facade->render($content, $stylesheet);

        return $content;
    }

}
