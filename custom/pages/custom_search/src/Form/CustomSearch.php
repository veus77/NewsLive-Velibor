<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/13/17
 * Time: 9:58 AM
 */

namespace Drupal\custom_search\Form;

use Drupal\Core\Database\Query\Condition;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CustomSearch extends FormBase
{


    public function getFormId()
    {
        return 'custom_search';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $form = [];

        $form['#method'] = 'GET';

	    $form['node_title'] = [
		    '#type' => 'textfield',
		    '#size' => 32,
		    '#placeholder' => t('Search by title ...'),
	    ];

	    $form['submit'] = array(
		    '#type' => 'submit',
		    '#value' => $this->t('Search'),
	    );

        $form['lifestyles'] = array(
            '#type' => 'checkbox',
            '#title' => $this->t('LifeStyle'),
            '#prefix' => '<div class="lifestyle">',

        );

        $form['lifestyle-terms'] = array(
            '#type' => 'checkboxes',
            '#options' => $this->_functionLifestyle(),
            '#multiple' => TRUE,
            '#default_value' => isset($_GET['lifestyle-terms']) ? $_GET['lifestyle-terms'] : [],
            '#suffix' => '</div>',
        );

        $form['technologies'] = array(
            '#type' => 'checkbox',
            '#title' => $this->t('Technology'),
            '#prefix' => '<div class="technology">',
        );

        $form['technology-terms'] = array(
            '#type' => 'checkboxes',
            '#options' => $this->_functionTechnology(),
            '#default_value' => isset($_GET['technology-terms']) ? $_GET['technology-terms'] : [],
            '#suffix' => '</div>',
        );

        $data = $this->buildContent();


        $form['#content']['data'] = $data;

        $form['#theme'] = 'custom_search';

	    $form['pager'] = array(
		    '#type' => 'pager',
	    );
        return $form;
    }

    public function _functionLifestyle()
    {
        $query = \Drupal::database()->select('taxonomy_term_field_data', 'ttf');
        $query->condition('ttf.vid', 'lifestyles_taxonomy', '=');
        $query->addField('ttf', 'name');
        $query->addField('ttf', 'tid');

        $lifeStyleTerms = $query->execute()->fetchAll();
        $term = [];

        foreach ($lifeStyleTerms as $result) {
            $query=\Drupal::database()->select('taxonomy_index', 'ti');
            $query->condition('ti.tid',$result->tid,'=');
            $query->addField('ti','nid');
            $query->execute()->fetchAll();
            $cntResult=$query->execute()->fetchAll();
            $count = count($cntResult);

            $term[$result->tid] = $result->name. '('.$count.')';
        }
        return $term;

    }

    public function _functionTechnology()
    {
        $query = \Drupal::database()->select('taxonomy_term_field_data', 'ttf');
        $query->condition('ttf.vid', 'technology', '=');
        $query->addField('ttf', 'name');
        $query->addField('ttf', 'tid');
        $lifeStyleTerms = $query->execute()->fetchAll();
        $term = [];

        foreach ($lifeStyleTerms as $result) {
            $query=\Drupal::database()->select('taxonomy_index', 'ti');
            $query->condition('ti.tid',$result->tid,'=');
            $query->addField('ti','nid');
            $query->execute()->fetchAll();
            $cntResult=$query->execute()->fetchAll();
            $count = count($cntResult);
            $term[$result->tid] = $result->name . '('.$count.')';
        }
        return $term;

    }

    private function buildContent()
    {
        $query = \Drupal::database()->select('node_field_data', 'nf');
        $query->addField('nf', 'created');
        $query->leftJoin('taxonomy_index', 'ti',
            'ti.nid=nf.nid');

        $query->leftJoin('taxonomy_term_field_data', 'ttf',
            'ttf.tid=ti.tid');

        $query->addField('ttf', 'name', 'taxonomy');

        $query->addField('nf', 'nid');
        $query->addField('nf', 'title', 'title');

        $query->innerJoin('users_field_data', 'u',
            'nf.uid = u.uid');
        $query->addField('u', 'name', 'user');

        $query->innerJoin('node__field_header_image', 'fi',
            'nf.nid = fi.entity_id');

        $query->innerJoin('file_managed', 'file',
            'file.fid = fi.field_header_image_target_id');
        $query->addField('file', 'uri', 'image');

        $query->innerJoin('node__body', 'b',
            'b.entity_id = nf.nid');
        $query->addField('b', 'body_value', 'body');

        if (isset($_GET['lifestyle-terms']) && isset($_GET['technology-terms'])) {
        $condition = new Condition('OR' );
        $condition->condition('ttf.tid', $_GET['lifestyle-terms'], 'IN');
        $condition->condition('ttf.tid', $_GET['technology-terms'], 'IN');

        $query->condition($condition);
        } else if (isset($_GET['lifestyle-terms'])) {
            $query->condition('ttf.tid', $_GET['lifestyle-terms'], 'IN');
        } else if (isset($_GET['technology-terms'])) {
            $query->condition('ttf.tid', $_GET['technology-terms'], 'IN');
        }


        if (!empty($_GET['node_title'])) {

            $query->condition('nf.title', '%'.$_GET['node_title'].'%', 'LIKE');
            $condition = new Condition('OR' );
        }

	    $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(4);

	    $results = $query->execute()->fetchAll();
        $data = [];

        foreach ($results as $result) {
            $image = $result->image;
            $url = \Drupal\image\Entity\ImageStyle::load('news_header_image')->buildUrl($image);
            $data[] = [
                'nid' => $result->nid,
                'title' => $result->title,
                'created' => date('F d, Y', $result->created),
                'taxonomy' => $result->taxonomy,
                'user' => $result->user,
                'image' => $url,
                'body' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->body)), 0, 192),
            ];
        }
        return $data;

    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {

    }

}