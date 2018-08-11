{extends file='page.tpl'}
{block name='page_content'}
{capture name=path}
    {l s='Testimonials List' mod='pk_testimonials'}
{/capture}
{$testimonials_list nofilter}
{/block}