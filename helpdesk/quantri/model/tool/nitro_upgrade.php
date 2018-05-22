<?php
class ModelToolNitroUpgrade extends ModelToolNitro {
  public function run_upgrade() {
      if (
          !empty($this->request->post['Nitro']['PageCache']['ClearCacheOnProductEdit']) && 
          $this->request->post['Nitro']['PageCache']['ClearCacheOnProductEdit'] == 'yes'
      ) {
          try {
              $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "nitro_product_cache");
              $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "nitro_category_cache");
              initNitroProductCacheDb();
          } catch (Exception $e) {}
      }
  }
}
