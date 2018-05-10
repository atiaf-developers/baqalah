<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pages extends MyModel {

    protected $table = 'pages';

    public static function getPages($parentId = 0) {
        $pages = static::where('active', 1)
                ->orderBy('this_order', 'asc')
                ->get();
        $branch = array();
        foreach ($pages as $element) {

            if ($element->parent_id == $parentId) {
                $children = static::getPages($element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public function sideBarHtml($pages) {
        $markup = "";
        foreach ($pages as $page) {

            if (\Permissions::check($one->name, 'open')) {
                $markup .= '<li class="nav-item start ' . $main_menu_active . '">';
                $url = (!emptyArray($page->children)) ? 'javascript:;' : url("admin/$page->controller");
                $markup .= '<a href="' . $url . '" class="nav-link nav-toggle">';
                $markup .= '<i class="icon-home"></i>';
                $markup .= '<span class="title"><?= _lang($value->name); ?></span>';
                 if (isset($page->children) && !emptyArray($page->children)) {
                    $markup .= ' <span class="arrow"></span>';
                }
                $markup .= ' </a>';
                if (isset($page->children) && !emptyArray($page->children)) {
                    $markup .= '<ul class="sub-menu">';

                    foreach ($page->children as $one) {


                        if (\Permissions::check($one->name, 'open')) {
                            $markup .= '<li class="' . $active_li . '">';
                            $markup .= '<a  class="nav-link " href="' . url("admin/$one->controller") . '">' . _lang($one->name);
                            $markup .= '</a>';
                            $markup .= '</li>';
                        }
                    }
                    $markup .= '</ul>';
                }
            }
        }

        return $markup;
    }

    public function text() {
        if ($node->level == 1) {
            $markup .= "<a href='" . site_url($path->full_path) . "'>";
            $markup .= '<i class="perspe" style="background:' . $mainCategoryColor . ';"></i>';
            $markup .= '<span>' . $node->text . '</span>';
            $markup .= "</a>";
        } else {
            $markup .= "<a href='" . site_url($path->full_path) . "' onmouseout='this.style.background=\"#fff\"' onmouseover='this.style.background=\"$this->mainCategoryColor\"'>";
            $markup .= $node->text;
            $markup .= "</a>";
        }

        if (isset($node->children)) {
            $markup .= '<ul style="border: 1px solid ' . $mainCategoryColor . '; border-top: 5px solid ' . $mainCategoryColor . ';">';
//                $markup .= '<li class="iconrig" style="background: #93342A;"><i class="fa fa-book" aria-hidden="true"></i></li>';
            $markup .= '<li class="iconrig" style="background: ' . $mainCategoryColor . ';"><img src="' . base_url('uploads/icons/' . $node->icon) . '"/></li>';
            $markup .= $this->treeOutHtml($node->children, $mainCategoryColor);
            $markup .= "</ul>";
        }
        $markup .= "</li>";
        $mainCategoryColor = false;
    }

}
