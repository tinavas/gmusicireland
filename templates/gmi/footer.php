<section class="sudo-footer">
    <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">&nbsp;</div>
</section>

<?php if ($this->countModules('footer-menu')) : ?>
    <!-- Begin Footer Menu -->
    <section class="nav-footer">
        <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
            <jdoc:include type="modules" name="footer-menu" style="none" />
        </div>
    </section>
    <!-- End Footer Menu -->
<?php endif; ?>

<?php if ($this->countModules('footer-search')) : ?>
    <!-- Begin Breadcrumbs -->
    <section class="footer-search">
        <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
            <jdoc:include type="modules" name="footer-search" style="none" />
        </div>
    </section>
    <!-- End Breadcrumbs -->
<?php endif; ?>

<?php if ($this->countModules('footer-menu') && $this->countModules('footer-breadcrumbs')) : ?>
    <section class="sudo-footer-spacer">
        <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">&nbsp;</div>
    </section>
<?php endif; ?>

<?php if ($this->countModules('footer-breadcrumbs')) : ?>
    <!-- Begin Breadcrumbs -->
    <section class="breadcrumbs">
        <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
            <jdoc:include type="modules" name="footer-breadcrumbs" style="none" />
        </div>
    </section>
    <!-- End Breadcrumbs -->
<?php endif; ?>

<footer class="footer" role="contentinfo">
	<div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
		<div class="row-fluid">
			<section class="footer-socialmedia span3">
				<jdoc:include type="modules" name="footer-socialmedia" style="none" />
			</section>
			<section class="footer-newsfeed span3">
				<jdoc:include type="modules" name="footer-newsfeed" style="none" />
			</section>
			<section class="footer-corporate span3">
			<jdoc:include type="modules" name="footer-corporate" style="none" />
			</section>
			<section class="footer-subscribe span3">
			<jdoc:include type="modules" name="footer-subscribe" style="none" />
			</section>
		</div>
		<hr />
		<jdoc:include type="modules" name="footer" style="none" />
		<div class="row-fluid">
			<section class="footer-copy span4">
				<p>
					&copy; <?php echo date('Y'); ?> <?php echo $sitename; ?>
					<br />All Rights Reserved
				</p>
			</section>
			<section class="footer-backtop span4">
					<jdoc:include type="modules" name="back-top" style="none" />
			</section>
			<section class="footer-logo span4">
					<jdoc:include type="modules" name="footer-logo" style="none" />
			</section>			
		</div>
	</div>
</footer>