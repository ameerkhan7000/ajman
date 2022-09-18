<?php

namespace Drupal\map_listing\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Map listing.
 *
 * @ingroup map_listing
 */
class DynamicMap extends FormBase {

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;
  
  /**
   * Constructor.
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'map_listing_autotextfields';
  }

  /**
   * {@inheritdoc}
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'map_listing/map_listing.map';
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'places', 'status' => 1]);
    foreach ($terms as $term) {
      $term_data[$term->id()] = $term->getName();
      $term_search_data[$term->id()] = $term->get('field_search_term')->value;
    }
    $form['#attributes'] = ['class' => ['row']];
    $form['places'] = [
      '#type' => 'radios',
      '#options' => $term_data,
      '#ajax' => [
        'callback' => '::textfieldsCallback',
        'wrapper' => 'map-wrapper',
        'effect' => 'fade',
      ],
      '#prefix' => '<div class="col-2">',
      '#suffix' => '</div">'
    ];

    $form['map_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'map-wrapper'],
    ]; 
    $form['map_wrapper']['map'] = [
      '#type' => 'markup',
      '#prefix' => '<div id="map-container">',
      '#suffix' => '</div">'
    ];
    $settings = $this->configFactory->get('map_listing.settings');
    $api_url = $settings->get('map_places_api');
    $api_key = $settings->get('map_places_api_key');
    $name = '';
    $points = []; 
    if ($form_state->getValue('places')) {
      $user_input = $form_state->getValue('places');
      $name = $term_search_data[$user_input];
      $name = $name. ' in Ajman';
      $url = $api_url . '?&key=' . $api_key . '&sort_point=55.00000, 25.000000&fields=items.point&page_size=10&q=' . $name;
      $jsonData = json_decode(file_get_contents($url));
      $points = [];
      if ($jsonData->result->items) {
        foreach ($jsonData->result->items as $key => $value) {
          $points[$key][] = $value->point->lon;
          $points[$key][] = $value->point->lat;
        }
      }
    }

    $form['map_wrapper']['#attached']['drupalSettings']['map_points'] = $points; 
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Callback for map_listing.
   */
  public function textfieldsCallback($form, FormStateInterface $form_state) {
    return $form['map_wrapper'];
  }

}
