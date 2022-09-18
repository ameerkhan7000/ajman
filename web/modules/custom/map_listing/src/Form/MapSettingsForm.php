<?php

namespace Drupal\map_listing\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure map_listing settings for this site.
 */
class MapSettingsForm extends ConfigFormBase {

  /** 
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'map_listing.settings';

  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'map_listing_admin_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS); 
    $form['map_places_api'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Map Places API Url'),
      '#default_value' => $config->get('map_places_api'),
    ]; 
    $form['map_places_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Map Places API Key'),
      '#default_value' => $config->get('map_places_api_key'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->config(static::SETTINGS)
      ->set('map_places_api', $form_state->getValue('map_places_api'))
      ->set('map_places_api_key', $form_state->getValue('map_places_api_key'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}