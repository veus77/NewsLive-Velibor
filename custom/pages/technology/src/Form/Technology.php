<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 12/8/17
 * Time: 2:55 PM
 */

namespace Drupal\technology\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


class Technology extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'technology';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $form = [];

        $form['#method'] = 'GET';

        $data = $this->buildContent();

        $form['#content']['data'] = $data;

        $form['#theme'] = 'technology';

        $form['pager'] = array(
            '#type' => 'pager',
        );

        return $form;
    }

    /**
     * Build Page Content
     *
     * @return array
     */
    private function buildContent()
    {

        $query = \Drupal::database()->select('node_field_data', 'n');
        $query->condition('n.type', 'news', '=');
        $query->condition('n.status', '1', '=');
        $query->addField('n', 'title');
        $query->addField('n', 'created');
        $query->addField('n', 'nid');


        $query->innerJoin('taxonomy_index', 'ti',
            'ti.nid = n.nid');
        $query->innerJoin('taxonomy_term_field_data', 'ttfd',
            'ttfd.tid = ti.tid');
        $query->condition('ttfd.vid', 'technology', '=');

        $query->addField('ttfd', 'name', 'taxonomy');

        $query->innerJoin('node__field_header_image', 'fi',
            'n.nid = fi.entity_id');
        $query->innerJoin('file_managed', 'file',
            'file.fid = fi.field_header_image_target_id');
        $query->addField('file', 'uri', 'image');

        $query->innerJoin('users_field_data', 'u',
            'n.uid = u.uid');
        $query->addField('u', 'name', 'user');

        $query->innerJoin('node__body', 'b',
            'b.entity_id = n.nid');
        $query->addField('b', 'body_value', 'body');

        $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(3);

        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ($results as $result) {
            $image=$result->image;
            $url = \Drupal\image\Entity\ImageStyle::load('news_header_image')->buildUrl($image);
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' .$result->nid);
            $data[] = [
                'nid' => $alias,
                'title' => $result->title,
                'image' => $url,
                'taxonomy' => $result->taxonomy,
                'user' => $result->user,
                'created' => date('F d, Y',$result->created),
                'body' => substr(strip_tags(str_replace(array("\r", "\n"), '', $result->body)), 0, 192),
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Display result.
        foreach ($form_state->getValues() as $key => $value) {
            drupal_set_message($key . ': ' . $value);
        }

    }
}