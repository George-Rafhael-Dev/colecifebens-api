#!/bin/sh

# para se der erro
set -e

echo "Iniciando container..."

if [ ! -f .env ]; then
  echo "Criando .env..."
  cp .env.example .env
fi

if [ ! -d vendor ]; then
  echo "Instalando dependências (composer install)..."
  composer install
fi

if ! grep -q "^APP_KEY=base64:" .env; then
  echo "Gerando APP_KEY..."
  php artisan key:generate
fi

echo "Aguardando banco de dados..."
until nc -z db 3306; do
    echo "Banco ainda não disponível, aguardando..."
    sleep 2
done
echo "Banco disponível."

echo "Rodando migrations..."
php artisan migrate

echo "Subindo Laravel..."
exec php artisan serve --host=0.0.0.0 --port=8000