<script>
var translations = new Array();
translations['in_progress'] = ' {l s='(in progress)' js=1 mod='allegro'}';
translations['finished'] = '{l s='finished' js=1 mod='allegro'}';
translations['failed'] = '{l s='failed' js=1 mod='allegro'}';

$('.ajaxcall-recurcive').each(
	function(it, elm) {
		$(elm).click(
			function() {
				if (this.cursor == undefined)
					this.cursor = 0;

				if (this.legend == undefined)
					this.legend = $(this).html();

				if (this.running == undefined)
					this.running = false;

				if (this.running == true)
					return false;

				$('.ajax-message, .ajax-danger, .ajax-success').hide();

				this.running = true;

				$(this).html(this.legend+translations['in_progress']);
				$('.ajax-warning').show();

				$.ajax({
					url: this.href+'&cursor='+this.cursor,
					context: this,
					dataType: 'json',
					cache: 'false',
					success: function(res) {
						this.running = false;
						if (res.continue) {
                            this.cursor = this.cursor+1;
                            $(this).html(this.legend+translations['in_progress'].replace('%s', res.count));
                            $(this).click();
                            return;
						}

                        this.cursor = 0;
                        $('.ajax-warning').hide();
                        $(this).html(this.legend);
                        $('.ajax-success span').html(translations['finished']);
                        $('.ajax-success').show();
                        return;

					},
					error: function(res) {
						$('.ajax-warning').hide();
						$('.ajax-danger span').html(translations['failed']);
						$('.ajax-danger').show();
						$(this).html(this.legend);

						this.cursor = 0;
						this.running = false;
					}
				});
				return false;
			}
		);
	}
);
</script>
