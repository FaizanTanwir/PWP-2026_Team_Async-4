import { MigrationInterface, QueryRunner } from 'typeorm';

export class InitialSchema1770732805819 implements MigrationInterface {
  name = 'InitialSchema1770732805819';

  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(
      `CREATE TABLE "languages" ("id" SERIAL NOT NULL, "name" character varying NOT NULL, "code" character varying(10) NOT NULL, CONSTRAINT "UQ_9c0e155475f0aa782e4a6178969" UNIQUE ("name"), CONSTRAINT "UQ_7397752718d1c9eb873722ec9b2" UNIQUE ("code"), CONSTRAINT "PK_b517f827ca496b29f4d549c631d" PRIMARY KEY ("id"))`,
    );
    await queryRunner.query(
      `CREATE TABLE "attempts" ("id" SERIAL NOT NULL, "audio_url" text NOT NULL, "score" double precision NOT NULL DEFAULT '0', "created_at" TIMESTAMP NOT NULL DEFAULT now(), "sentenceId" integer, CONSTRAINT "PK_295ca261e361fd2fd217754dcac" PRIMARY KEY ("id"))`,
    );
    await queryRunner.query(
      `CREATE TABLE "words" ("id" SERIAL NOT NULL, "term" character varying NOT NULL, "lemma" character varying, "translation" character varying, CONSTRAINT "PK_feaf97accb69a7f355fa6f58a3d" PRIMARY KEY ("id"))`,
    );
    await queryRunner.query(
      `CREATE TABLE "sentence_words" ("sentence_id" integer NOT NULL, "word_id" integer NOT NULL, CONSTRAINT "PK_1276c9bb245dd6ca5ac0b788839" PRIMARY KEY ("sentence_id", "word_id"))`,
    );
    await queryRunner.query(
      `CREATE TABLE "sentences" ("id" SERIAL NOT NULL, "text_target" text NOT NULL, "text_source" text NOT NULL, "unitId" integer, CONSTRAINT "PK_9b3aec16318cf425aaddad6dd5f" PRIMARY KEY ("id"))`,
    );
    await queryRunner.query(
      `CREATE TABLE "units" ("id" SERIAL NOT NULL, "title" character varying NOT NULL, "courseId" integer, CONSTRAINT "PK_5a8f2f064919b587d93936cb223" PRIMARY KEY ("id"))`,
    );
    await queryRunner.query(
      `CREATE TABLE "courses" ("id" SERIAL NOT NULL, "title" character varying NOT NULL, "source_language_id" integer, "target_language_id" integer, CONSTRAINT "PK_3f70a487cc718ad8eda4e6d58c9" PRIMARY KEY ("id"))`,
    );
    await queryRunner.query(
      `ALTER TABLE "attempts" ADD CONSTRAINT "FK_23d05c1d12a3cff6c5f37aecfc8" FOREIGN KEY ("sentenceId") REFERENCES "sentences"("id") ON DELETE CASCADE ON UPDATE NO ACTION`,
    );
    await queryRunner.query(
      `ALTER TABLE "sentence_words" ADD CONSTRAINT "FK_beb95a0e4b0c0849a135df3e6a6" FOREIGN KEY ("sentence_id") REFERENCES "sentences"("id") ON DELETE CASCADE ON UPDATE NO ACTION`,
    );
    await queryRunner.query(
      `ALTER TABLE "sentence_words" ADD CONSTRAINT "FK_d680af839a5695c4c1c41e22a16" FOREIGN KEY ("word_id") REFERENCES "words"("id") ON DELETE CASCADE ON UPDATE NO ACTION`,
    );
    await queryRunner.query(
      `ALTER TABLE "sentences" ADD CONSTRAINT "FK_cc85373a64bcde109581205c9db" FOREIGN KEY ("unitId") REFERENCES "units"("id") ON DELETE CASCADE ON UPDATE NO ACTION`,
    );
    await queryRunner.query(
      `ALTER TABLE "units" ADD CONSTRAINT "FK_488039d526ad82da05a60e93556" FOREIGN KEY ("courseId") REFERENCES "courses"("id") ON DELETE CASCADE ON UPDATE NO ACTION`,
    );
    await queryRunner.query(
      `ALTER TABLE "courses" ADD CONSTRAINT "FK_ff0f83e9edee6868c06228c5121" FOREIGN KEY ("source_language_id") REFERENCES "languages"("id") ON DELETE RESTRICT ON UPDATE NO ACTION`,
    );
    await queryRunner.query(
      `ALTER TABLE "courses" ADD CONSTRAINT "FK_7e86af9e306ba2e3e6a9311852e" FOREIGN KEY ("target_language_id") REFERENCES "languages"("id") ON DELETE RESTRICT ON UPDATE NO ACTION`,
    );
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(
      `ALTER TABLE "courses" DROP CONSTRAINT "FK_7e86af9e306ba2e3e6a9311852e"`,
    );
    await queryRunner.query(
      `ALTER TABLE "courses" DROP CONSTRAINT "FK_ff0f83e9edee6868c06228c5121"`,
    );
    await queryRunner.query(
      `ALTER TABLE "units" DROP CONSTRAINT "FK_488039d526ad82da05a60e93556"`,
    );
    await queryRunner.query(
      `ALTER TABLE "sentences" DROP CONSTRAINT "FK_cc85373a64bcde109581205c9db"`,
    );
    await queryRunner.query(
      `ALTER TABLE "sentence_words" DROP CONSTRAINT "FK_d680af839a5695c4c1c41e22a16"`,
    );
    await queryRunner.query(
      `ALTER TABLE "sentence_words" DROP CONSTRAINT "FK_beb95a0e4b0c0849a135df3e6a6"`,
    );
    await queryRunner.query(
      `ALTER TABLE "attempts" DROP CONSTRAINT "FK_23d05c1d12a3cff6c5f37aecfc8"`,
    );
    await queryRunner.query(`DROP TABLE "courses"`);
    await queryRunner.query(`DROP TABLE "units"`);
    await queryRunner.query(`DROP TABLE "sentences"`);
    await queryRunner.query(`DROP TABLE "sentence_words"`);
    await queryRunner.query(`DROP TABLE "words"`);
    await queryRunner.query(`DROP TABLE "attempts"`);
    await queryRunner.query(`DROP TABLE "languages"`);
  }
}
