<!-- Manufacturers module -->
<div class="pk-categories">
  <div class="page-width relative oh">
    <h4 class="module-title"><span>{l s='Shop by Categories' mod='pk_categories'}</span></h4>
    <div class="pk-categories-list">
      <ul class="flex-container ul-clear">
      {foreach from=$categories item=cat}
        <li>
          <a href="{$cat->link}" class="pkcl-wrap" style="background-image:url({$cat->image})">
            <span class="pk-category-title">{$cat->name}</span>
          </a>
        </li>
      {/foreach}
      </ul>
    </div>
  </div>
</div>  
<!-- /Manufacturers module -->