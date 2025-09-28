DROP TABLE IF EXISTS public."Warehouse" CASCADE;
DROP TABLE IF EXISTS public."Socks" CASCADE;

CREATE TABLE public."Socks" (
    "skuId" UUID NOT NULL,
    "skuName" VARCHAR NOT NULL,
    "color" VARCHAR NOT NULL,
    "cottonPart" INTEGER NOT NULL,
    CONSTRAINT "socksCottonPartCheck" CHECK ((("cottonPart" >= 0) AND ("cottonPart" <= 100)))
);


CREATE TABLE public."Warehouse" (
    "opeationId" UUID NOT NULL,
    "skuId" UUID,
    "quantity" INTEGER NOT NULL,
    "operationType" VARCHAR NOT NULL,
    "createdAt" TIMESTAMPTZ DEFAULT now() NOT NULL,
    CONSTRAINT "warehouseOperationTypeCheck" CHECK ((("operationType")::text = ANY ((ARRAY['income'::VARCHAR, 'outcome'::VARCHAR])::text[]))),
    CONSTRAINT "warehouseQuantityCheck" CHECK ((quantity > 0))
);

ALTER TABLE public."Socks" 
ADD CONSTRAINT "socksPrimary" PRIMARY KEY ("skuId");

ALTER TABLE public."Warehouse"
ADD CONSTRAINT "warehousePrimaryKey" PRIMARY KEY ("opeationId");

ALTER TABLE public."Warehouse"
ADD CONSTRAINT "warehouseSkuIdForeingKey" FOREIGN KEY ("skuId") REFERENCES "Socks"("skuId");