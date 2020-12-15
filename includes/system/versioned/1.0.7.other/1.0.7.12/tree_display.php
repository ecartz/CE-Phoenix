<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license GNU General Public License; http://www.oscommerce.com/gpllicense.txt
 */

  class tree_display extends displayable_tree_accessor {

    protected function _buildBranch($parent_id, $level = 0) {
      $result = ((($level === 0) && ($this->parent_group_apply_to_root === true)) || ($level > 0))
              ? $this->parent_group_start_string
              : '';

      foreach ( $this->tree->get_children($parent_id) ?? [] as $id ) {
        $name = $this->tree->get($id, 'name');
        $link = ( $this->breadcrumb_usage === true )
              ? $this->buildBreadcrumb($id)
              : $id;

        $result .= $this->child_start_string;

        if ( $this->tree->get_children($id) ) {
          $result .= $this->parent_start_string;
        }

        if ( $level === 0 ) {
          $result .= $this->root_start_string;
        }

        if ( ($this->follow_path === true) && in_array($id, $this->path_array) ) {
          $link_title = $this->path_start_string . $name . $this->path_end_string;
        } else {
          $link_title = $name;
        }

        $result .= '<a class="list-group-item list-group-item-action" href="' . $this->tree->build_path_link($link) . '">';
        $result .= str_repeat($this->spacer_string, $this->spacer_multiplier * $level);
        $result .= $link_title . '</a>';

        if ( $level === 0 ) {
          $result .= $this->root_end_string;
        }

        if ( $this->tree->get_children($id) ) {
          $result .= $this->parent_end_string;

          if ( (($this->max_level == '0') || ($this->max_level > $level+1))
            && ( ( $this->follow_path !== true ) || in_array($id, $this->path_array) ) )
          {
            $result .= $this->_buildBranch($id, $level+1);
          }
        }

        $result .= $this->child_end_string;
      }

      if ((($level === 0) && ($this->parent_group_apply_to_root === true)) || ($level > 0)) {
        $result .= $this->parent_group_end_string;
      }

      return $result;
    }

    public function buildBranchArray($parent_id, $level = 0, $result = []) {
      foreach ($this->_data[$parent_id] as $id => $data) {
        $link = $this->breadcrumb_usage
              ? $this->buildBreadcrumb($id)
              : $id;

        $result[] = [
          'id' => $link,
          'image' => $data['image'],
          'title' => str_repeat($this->spacer_string, $this->spacer_multiplier * $level) . $data['name'],
        ];

        if (isset($this->_data[$id])
          && (($this->max_level == '0') || ($this->max_level > $level+1))
          && (($this->follow_path !== true) || in_array($id, $this->path_array)))
        {
          $result = $this->buildBranchArray($id, $level+1, $result);
        }
      }

      return $result;
    }

    public function buildBreadcrumb($id, $level = 0) {
      if (null === $id) {
        error_log("Id: [$id]");
        ob_start();
        debug_print_backtrace();
        error_log(ob_get_clean());
        return '';
      }
      $parent = $this->tree->get_parent_id($id);
      return ($this->tree->get_root_id() == $parent)
             ? $id
             : $this->buildBreadcrumb($parent, $level+1)
               . $this->breadcrumb_separator
               . $id;
    }

/**
 * Magic function; return a formated string representation of the category structure relationship data
 *
 * This is used when echoing the class object, eg:
 *
 * echo $tree;
 *
 * @access public
 * @return string
 */
    public function __toString() {
      return $this->_buildBranch($this->tree->get_root_id());
    }

  }
