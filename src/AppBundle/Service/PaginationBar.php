<?php
// src/AppBundle/Service/PaginationBar.php
namespace AppBundle\Service;

class PaginationBar
{
    private $customParameters = [
        'records_number'   => NULL,
        'records_per_page' => NULL,
        'current_page'     => NULL,
        'pages_step'       => NULL
    ];

    private $pagesNumber = NULL;

    private $paginationBar = [];

    public function setParameters($records_number, $records_per_page, $current_page, $pages_step)
    {
        $this->customParameters['records_number']   = $records_number;
        $this->customParameters['records_per_page'] = $records_per_page;

        $this->pagesNumber = $this->getPagesNumber(
            $this->customParameters['records_number'],
            $this->customParameters['records_per_page']
        );

        if( $this->pagesNumber === FALSE ) {
            return FALSE;
        }

        if( !$this->is_page_exists($current_page, $this->pagesNumber) ) {
            return FALSE;
        } else {
            $this->customParameters['current_page'] = $current_page;
        }

        $this->customParameters['pages_step'] = $pages_step;

        return TRUE;
    }

    public function setPaginationBar()
    {
        $paginationBar['current_page'] = $this->customParameters['current_page'];
        $paginationBar['pages_number'] = $this->pagesNumber;

        $paginationBar['navigation_items'] = $this->buildNavigationItems(
            $this->customParameters['pages_step'],
            $this->customParameters['current_page'],
            $this->pagesNumber
        );

        $paginationBar['navigation_required'] = ( count($paginationBar['navigation_items']) > 1 ) ? TRUE : FALSE;

        $paginationBar['side_buttons'] = $this->getSideButtons($this->customParameters['current_page'], $this->pagesNumber);

        $this->paginationBar = $paginationBar;
    }

    public function getCustomParameters()
    {
        if( empty($this->customParameters) ) {
            throw new \RuntimeException("Trying to get an empty property");
        }

        return $this->customParameters;
    }

    public function getPageInformation($records_count)
    {
        if( empty($this->paginationBar) ) {
            throw new \RuntimeException("Trying to get an empty property");
        }

        $firstRecord = ($this->customParameters['current_page'] * $this->customParameters['records_per_page'] - $this->customParameters['records_per_page']) + 1;
        $lastRecord  = ($firstRecord + $records_count) - 1;

        return [
            'totalRecords' => $this->customParameters['records_number'],
            'firstRecord'  => $firstRecord,
            'lastRecord'   => $lastRecord,
            'currentPage'  => $this->customParameters['current_page']
        ];
    }

    public function getPaginationBar()
    {
        if( empty($this->paginationBar) ) {
            throw new \RuntimeException("Trying to get an empty property");
        }

        return $this->paginationBar;
    }

    private function getPagesNumber($records_number, $records_per_page)
    {
        return ( ($records_per_page !== 0) ) ? (int)ceil($records_number / $records_per_page) : FALSE;
    }

    private function is_page_exists($current_page, $pages_number)
    {
        return ( in_array($current_page, range(1, $pages_number)) ) ? TRUE : FALSE;
    }

    private function buildNavigationItems($pages_step, $current_page, $pages_number)
    {
        if( !$pages_number ) {
            return FALSE;
        }

        $pagination = array();

        if( in_array($current_page, range(1, $pages_step)) )
        {
            if( $pages_number <= $pages_step ) {
                $pagination = range(1, $pages_number);
            } else {
                $pagination = array_merge(range(1, $pages_step+1), array('separator',$pages_number));
            }
        } elseif( in_array($current_page, range($pages_number-$pages_step+1, $pages_number)) ) {
            $pagination = array_merge(
                array(1,'separator'),
                range($pages_number-$pages_step, $pages_number)
            );
        } elseif( in_array($current_page, range($pages_step+1, $pages_number-$pages_step)) ) {
            $side_step = ceil($pages_step / 2) - 1;

            $pagination = array_merge(
                array(1,'separator'),
                range($current_page - $side_step, $current_page + $side_step),
                array('separator',$pages_number)
            );
        }

        return ( !empty($pagination) ) ? $pagination : FALSE;
    }

    private function getSideButtons($current_page, $pages_number)
    {
        $side_buttons['page_prev'] = ( ($current_page - 1) > 0 ) ? $current_page - 1 : NULL;
        $side_buttons['page_next'] = ( ($current_page + 1) <= $pages_number ) ? $current_page + 1 : NULL;

        return $side_buttons;
    }
}
