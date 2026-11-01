</main>
<!-- Textos do idioma atual, para o JavaScript usar (JSON_HEX_TAG impede fechar o </script>) -->
<script id="textos" type="application/json"><?= json_encode(textos(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script src="<?= asset("assets/js/app.js") ?>"></script>
</body>
</html>
