# cakephp-pdf-view
A PDF view for CakePHP

This package provides PDF generation for CakePHP3 using [psliwa/php-pdf](https://packagist.org/packages/psliwa/php-pdf), a PDF and graphic files generator library for PHP.

The PDF library employs XML files with enhanced HTML tags for content and XML files with custom attributes for layout and can be installed with Composer.

Setting up a PDF view in Cakephp3 is quite simple; all it needs is

* a Component to handle the Request
* a View to enable the conversion
* some Initialization in the Controller
* The registration of the .pdf extension in the Router

In the View, set a custom extension for the XML templates, to avoid having IDE like PHPStorm complaining about language-related inspections:

    $this->_ext = '.xctp';

The PdfComponent detects a PDF request and, if so, sets the PDF View to handle it.

In the Controllers initialize() method, load the PDFComponent and add a detector for the PDF view:

    public function initialize()
    {
      parent::initialize();
      $this->loadComponent('RequestHandler');
      ...
      $this->loadComponent('Pdf.Pdf', ['viewClass' => 'Pdf', 'autoDetect' => true]);
      Request::addDetector('pdf', ['accept' => ['application/pdf'], 'param' => '_ext', 'value' => 'pdf']);
    }

Finally, add the .pdf extension to our router:

    Router::extensions(['pdf']);

That’s basically it! Now you can use the same conventions as with regular template files. A PDF request to `App\Controller\FooController::baz()` with the URL `/foo/baz.pdf` will expect its template under `Template/Foo/pdf/baz.xctp` with its stylesheet under `Template/Foo/pdf/baz.style.xctp`.

* Controller Action: `App\Controller\FooController::baz()`
* URL: `/foo/baz.pdf`
* Template: `Template/Foo/pdf/baz.xctp`
* Stylesheet: `Template/Foo/pdf/baz.style.xctp`

In your Action, set variables and use them in your .xctp template as in any other template.
